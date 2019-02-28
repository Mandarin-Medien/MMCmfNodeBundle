<?php

namespace MandarinMedien\MMCmfNodeBundle\Templating;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeDefinitionResolver;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeTemplateResolver;
use MandarinMedien\MMCmfNodeBundle\Resolver\TemplateDefinitionResolver;
use Symfony\Bundle\TwigBundle\TwigEngine;
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
     * @var \Twig_Environment
     */
    protected $twig;


    /**
     * TemplateManager constructor.
     * @param TwigEngine $twig
     * @param KernelInterface $kernel
     */
    public function __construct(TwigEngine $twig, KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->twig = $twig;
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

        $template = $node->getTemplate()
            ?: $this->getDefaultTemplate($node);

        return $this->resolveLocalTemplate($template, $node) ?? $template;

    }

    /**
     * get the bundle name form entity namespace
     *
     * @param $entityNamespace
     * @param $bundles
     * @return int|string|null
     * @throws \ReflectionException
     */
    /**
     * get the bundle name form entity namespace
     *
     * @param $entityNamespace
     * @param $bundles
     * @return int|string|null
     * @throws \ReflectionException
     */
    protected function getBundlePrefixFromEntity($entityNamespace)
    {
        $dataBaseNamespace = substr($entityNamespace, 0, strpos($entityNamespace, '\\Entity'));

        foreach ($this->kernel->getBundles() as $type => $bundle) {
            $bundleRefClass = new \ReflectionClass($bundle);
            if ($bundleRefClass->getNamespaceName() === $dataBaseNamespace) {
                return '@'.preg_replace('/Bundle$/', '', $type);
            }
        }

        return null;
    }


    /**
     * get the default template path if no templates are configured
     *
     * @param string $bundleName
     * @return string
     * @throws
     */
    public function getDefaultTemplate(NodeInterface $node)
    {

        $reflection = new \ReflectionClass(get_class($node));
        $name = $reflection->getShortName();
        $namespace = $reflection->getNamespaceName();
        $bundlePrefix = $this->getBundlePrefixFromEntity($namespace);

        return $bundlePrefix . '/cmf/' . $name . '/' . $name . '_default.html.twig';
    }


    /**
     * Search for Templates which are locally overwritten
     *
     * @param $path
     * @param $node
     * @return string|null
     */
    protected function resolveLocalTemplate($path, $node)
    {
        // check for local override
        $idList = [$node->getId()];

        while ($node = $node->getParent())
            $idList[] = $node->getId();


        $localTemplate = null;

        foreach(array_reverse($idList) as $id) {

            $localTemplateTmp = preg_replace('/^(@[a-zA-Z0-9_]+)/', "$1/" . $id, $path);
            
            $localTemplate = $this->twig->exists($localTemplateTmp)
                ? $localTemplateTmp
                : $localTemplate;
        }

        return $localTemplate;
    }



}