<?php

namespace MandarinMedien\MMCmfNodeBundle\Templating;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeDefinitionResolver;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class TemplateManager
 *
 * handle the templates of TemplatableNodeInterface entities
 *
 * @package MandarinMedien\MMCmfNodeBundle\Templating
 */
class TemplateManager
{

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var array
     */
    protected $templates;


    /**
     * @var NodeDefinitionResolver
     */
    protected $nodeDefinitionResolver;

    /**
     * TemplateManager constructor.
     * @param EntityManagerInterface $manager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $manager, KernelInterface $kernel, NodeDefinitionResolver $nodeDefinitionResolver)
    {

        $this->manager = $manager;
        $this->kernel = $kernel;
        $this->nodeDefinitionResolver = $nodeDefinitionResolver;
        $this->templates = array();
    }


    /**
     * register template
     *
     * @param string $class fully qualified class-name
     * @param string $name name of the template
     * @param string $template template path
     * @return $this
     */
    public function registerTemplate($class, $name, $template)
    {
        $this->templates[$class][$name] = $template;
        return $this;
    }


    /**
     * get a list of all templates assigned to the given class
     *
     * @param NodeInterface $node
     * @return mixed
     */
    public function getTemplates(NodeInterface $node)
    {
        if($definition = $this->nodeDefinitionResolver->resolve($node))
        {
            return $definition->getTemplates();
        }

        return null;
    }


    /**
     * get the assigned template of the given TemplatableNodeInterface
     * handles the template selection if no template is assigned
     *
     * @param TemplatableNodeInterface $node
     * @return mixed|string
     * @throws
     */
    public function getTemplate(TemplatableNodeInterface $node)
    {

        $template = $node->getTemplate();

        if (!$template) {

            $meta = $this->manager->getClassMetadata(get_class($node));

            if (!empty($this->templates[$meta->name]) && count($this->templates[$meta->name]) > 0)
                $template = reset($this->templates[$meta->name]);

            else {
                $name = trim(str_replace($meta->namespace, '', $meta->name), '\\');
                $bundleName = $this->getBundleNameFromEntity($meta->namespace, $this->kernel->getBundles());
                $template = $this->getDefaultTemplate($name, $bundleName);
            }
        }

        return $template;

    }

    /**
     * get the bundle name form entity namespace
     *
     * @param $entityNamespace
     * @param $bundles
     * @return int|string|null
     * @throws \ReflectionException
     */
    protected static function getBundleNameFromEntity($entityNamespace, $bundles)
    {
        $dataBaseNamespace = substr($entityNamespace, 0, strpos($entityNamespace, '\\Entity'));

        foreach ($bundles as $type => $bundle) {
            $bundleRefClass = new \ReflectionClass($bundle);
            if ($bundleRefClass->getNamespaceName() === $dataBaseNamespace) {
                return $type;
            }
        }

        return null;
    }


    /**
     * get the default template path if no templates are configured
     *
     * @param string $bundleName
     * @return string
     */
    public function getDefaultTemplate($className, $bundleName = "MMCmfNodeBundle")
    {
        return $bundleName . ':cmf:' . $className . '/' . $className . '_default.html.twig';
    }

}