<?php

namespace MandarinMedien\MMCmfNodeBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Exception\InvalidArgumentException;


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
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    private $meta;

    /**
     * @var array
     */
    protected $childDefintions;


    /**
     * ContentNodeFactory constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->childDefintions = [];
        $this->manager = $manager;
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
        if($instance instanceof NodeInterface)
        {
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
            function($class) use ($subclasses)
            {
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
        if ($class = ($this->getMeta()->discriminatorMap[$discriminator])) {
            return $class;
        } else {
            throw new \Exception('class not found');
        }
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
        if(!$reflectionParent->implementsInterface(NodeInterface::class)) {
            throw new \InvalidArgumentException('Parent Node must implement '.NodeInterface::class);
        }

        $reflectionParent = new \ReflectionClass($child);
        if(!$reflectionParent->implementsInterface(NodeInterface::class)) {
            throw new \InvalidArgumentException('Child Node must implement '.NodeInterface::class);
        }


        if(!isset($this->childDefintions[$parent])) $this->childDefintions[$parent] = [];
        if(!in_array($child, $this->childDefintions[$parent])) $this->childDefintions[$parent][] = $child;

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
        if(!$reflection->implementsInterface(NodeInterface::class))
            throw new \InvalidArgumentException('the given factory class "'.$rootClass.'"must implement '.NodeInterface::class);

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
        return $this->manager->getClassMetadata($this->rootClass);
    }

}