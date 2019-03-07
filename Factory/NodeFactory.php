<?php

namespace MandarinMedien\MMCmfNodeBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use MandarinMedien\MMCmfNodeBundle\Configuration\NodeDefinition;
use MandarinMedien\MMCmfNodeBundle\Configuration\NodeRegistry;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeDefinitionResolver;


class NodeFactory
{


    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $rootClass;


    /**
     * @var string
     */
    private $defaultIcon;


    /**
     * @var array stroes individual icons per class
     */
    protected $icons;


    /**
     * @var array
     */
    protected $childDefinitions;


    /**
     * @var NodeDefinitionResolver
     */
    protected $definitionResolver;


    /**
     * @var array
     */
    protected $classIndex;


    /**
     * NodeFactory constructor.
     * @param EntityManagerInterface $manager
     * @param NodeDefinitionResolver $definitionResolver
     */
    public function __construct(EntityManagerInterface $manager, NodeDefinitionResolver $definitionResolver)
    {
        $this->childDefintions = [];
        $this->icons = [];
        $this->manager = $manager;
        $this->definitionResolver = $definitionResolver;
    }


    /**
     * create a new ContentNode instance by discriminator value
     * @param string $discriminator
     * @return NodeInterface
     * @throws \Exception
     */
    public function createNode($discriminator = 'default')
    {
        $reflection = new \ReflectionClass($this->getClassByDiscriminator($discriminator));

        $instance = $reflection->newInstance();
        if ($instance instanceof NodeInterface) {
            return $instance;
        }

        return null;
    }


    /**
     * get all available discriminator values of Node entity
     * @param array $exclude exclude specific discriminators
     * @return array
     */
    public function getDiscriminators($exclude = array())
    {

        // prefilter discriminators by subclasses
        $subclasses = $this->getMeta()->subClasses;

        $discriminators = ([$this->getMeta()->discriminatorValue => $this->getRootClass()] + array_filter(
                $this->getMeta()->discriminatorMap,
                function ($class) use ($subclasses) {
                    return in_array($class, $subclasses);
                }
            ));

        // filter discriminators by exlude array
        return array_diff(array_keys($discriminators), $exclude);
    }


    /**
     * get the discriminator value by the given instance
     * @param NodeInterface $node
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscriminatorByClass(NodeInterface $node)
    {
        return $this->manager->getClassMetadata(get_class($node))->discriminatorValue;
    }

    /**
     * get the discriminator value by the given className
     * @param string $className
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDiscriminatorByClassName($className)
    {
        return $this->manager->getClassMetadata($className)->discriminatorValue;
    }


    /**
     * get the ContentNode subclass by discriminator value
     * @param string $discriminator
     * @return string
     * @throws \Exception
     */
    public function getClassByDiscriminator($discriminator)
    {
        static $classes;

        if (empty($classes[$discriminator]))
            if ($className = ($this->getMeta()->discriminatorMap[$discriminator]))
                $classes[$discriminator] = $className;
            else
                throw new \Exception('class not found');

        return $classes[$discriminator];
    }


    /**
     * @param $parent
     * @param $child
     * @return $this
     * @throws \ReflectionException
     */
    public function addChildNodeDefinition($parent, $child)
    {

        $reflectionParent = new \ReflectionClass($parent);
        if (!$reflectionParent->implementsInterface(NodeInterface::class)) {
            throw new \InvalidArgumentException('Parent Node must implement ' . NodeInterface::class);
        }

        $reflectionParent = new \ReflectionClass($child);
        if (!$reflectionParent->implementsInterface(NodeInterface::class)) {
            throw new \InvalidArgumentException('Child Node must implement ' . NodeInterface::class);
        }


        if (!isset($this->childDefintions[$parent])) $this->childDefintions[$parent] = [];
        if (!in_array($child, $this->childDefintions[$parent])) $this->childDefintions[$parent][] = $child;

        return $this;
    }


    /**
     * get al list of configured child nodes
     * @param $parent
     * @return array
     */
    public function getChildNodeDefinition($parent)
    {
        return isset($this->childDefintions[$parent]) ? $this->childDefintions[$parent] : [];
    }


    /**
     * get the complete definition of children
     */
    public function getChildNodeDefinitions()
    {
        return $this->childDefintions;
    }


    /**
     * @param string $rootClass
     * @throws \ReflectionException|\InvalidArgumentException
     */
    public function setRootClass($rootClass)
    {
        $reflection = new \ReflectionClass($rootClass);
        if (!$reflection->implementsInterface(NodeInterface::class))
            throw new \InvalidArgumentException('the given factory class "' . $rootClass . '"must implement ' . NodeInterface::class);

        $this->rootClass = $rootClass;
    }

    /**
     * @return string
     */
    public function getRootClass()
    {
        return $this->rootClass;
    }

    protected function getMeta()
    {
        static $metaData;

        if (!$metaData)
            $metaData = $this->manager->getClassMetadata($this->rootClass);

        return $metaData;
    }


    /**
     * @param $class
     * @param $icon
     * @return $this
     */
    public function addIcon($class, $icon)
    {
        $this->icons[$class] = $icon;
        return $this;
    }

    /**
     * @return array
     */
    public function getIcons()
    {
        return $this->icons;
    }


    /**
     * @param string $defaultIcon
     */
    public function setDefaultIcon($defaultIcon)
    {
        $this->defaultIcon = $defaultIcon;
    }

    public function getIcon($class)
    {
        return isset($this->icons[$class]) ? $this->icons[$class] : $this->defaultIcon;

    }

    /**
     * @Todo: find better way to handle the relation between dytpe and classNames
     * @param NodeInterface|null $node
     * @return array
     * @throws
     */
    public function getTree(NodeInterface $node = null)
    {
        $nodes = $this->getNodes();


        /**
         * @Todo: Maybe do some caching here
         */
        $tree = $this->_buildTree($nodes);

        return $tree;
    }


    /**
     * build the node tree
     *
     * @param array $nodes
     * @param null $parent
     * @return array
     * @throws
     */
    public function _buildTree(&$nodes, $parent = null, $parentKey = null)
    {
        $tree = [];

        foreach($nodes as &$node) {

            $className = $this->getClassByDiscriminator($node['dtype']);

            // build the key for the node defintion
            $definitionKey = $parentKey
                ? ($parentKey.NodeDefinitionResolver::KEY_SEPARATOR.$className)
                : $className;

            /**
             * @var NodeDefinition $definition
             */
            $definition = $this->definitionResolver->resolveByKey($definitionKey);

            // add the class from dtype
            $node['className'] = $className;

            $node['definition'] = $definition;

            // set the key for resolving icons and possible child classes
            //$node['definitionKey'] = $definitionKey;

            // add the icon
            //$node['icon'] = $definition ? $definition->getIcon() : null;

            // add the children
            //$node['children'] = $definition['children'];

            if($node['parent'] === $parent) {
                $_node = $node;
                unset($node);
                $tree[] = array_merge($_node, ['children' => $this->_buildTree($nodes, $_node['id'], $definitionKey)]);
            }

        };


        // sort by positions
        usort($tree, function($a, $b) {

            if($a['position'] < $b['position']) {
                return -1;
            } elseif($a['position'] > $b['position']) {
                return 1;
            } else {
                return 0;
            }
        });

        return $tree;

    }


    protected function getNodes()
    {

        static $nodes;

        if(!$nodes) {
            $rootNodeMeta = $this->manager->getClassMetadata($this->getRootClass());

            $rsm = new ResultSetMapping();
            $rsm
                ->addScalarResult('id', 'id', 'integer')
                ->addScalarResult('position', 'position', 'integer')
                ->addScalarResult('visible', 'visible', 'boolean')
                ->addScalarResult('name', 'name', 'string')
                ->addScalarResult('parent_id', 'parent', 'integer')
                ->addScalarResult('dtype', 'dtype', 'string');

            $query = 'SELECT n.id, n.name, n.parent_id, n.position, n.visible, n.dtype FROM `' . $rootNodeMeta->getTableName() . '` as n';


            $query = $this->manager->createNativeQuery($query, $rsm);
            $nodes = $query->getScalarResult();
        }

        return $nodes;
    }


    /**
     * @return NodeDefinitionResolver
     */
    public function getDefinitionResolver(): NodeDefinitionResolver
    {
        return $this->definitionResolver;
    }


    /**
     * return a flatten list with all entities implementing NodeInterface
     * @return array
     */
    public function getClassIndex()
    {

        if(is_null($this->classIndex)) {
            $this->classIndex = [];
            /**
             * get a full list of all entities implementing NodeInterface
             * for resolving dtypes
             */
            foreach ($this->getDiscriminators() as $dtype)
                $classIndex[$this->getClassByDiscriminator($dtype)] = $dtype;
        }

        return $this->classIndex;
    }


    public function getNodeMeta($nodeInterfaceOrId)
    {
        $id = $nodeInterfaceOrId instanceof NodeInterface
            ? $nodeInterfaceOrId->getId()
            : $nodeInterfaceOrId;

        $node = array_filter($this->getNodes(), function ($node) use ($id) {
            return $id === $node['id'];
        });
        $node = reset($node);

    }


}
