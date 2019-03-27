<?php

namespace MandarinMedien\MMCmfNodeBundle\Templating;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Resolver\TemplateDefinitionResolver;
use MandarinMedien\MMSearchBundle\Serializer\Factory;
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
     * @var string Directory for local template overrides
     */
    protected $overrideDir;

    /**
     * @var Factory
     */
    protected $factory;


    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var TemplateDefinitionResolver
     */
    protected $definitionResolver;

    /**
     * TemplateManager constructor.
     * @param NodeFactory $factory
     * @param TwigEngine $twig
     * @param KernelInterface $kernel
     * @param string $overrideDir
     */
    public function __construct(NodeFactory $factory, TwigEngine $twig, KernelInterface $kernel, string $overrideDir, TemplateDefinitionResolver $definitionResolver)
    {
        $this->factory = $factory;
        $this->kernel = $kernel;
        $this->twig = $twig;
        $this->overrideDir = $overrideDir;
        $this->definitionResolver = $definitionResolver;
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
            $templateDefinitions = $this->definitionResolver->resolve($node);
            if (count($templateDefinitions) > 0)
                $template = $templateDefinitions[0]->getPath();
        }

        if (!$template)
            $template = $this->getDefaultTemplate($node);

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
    protected function getBundlePrefixFromEntity($entityNamespace)
    {
        $dataBaseNamespace = substr($entityNamespace, 0, strpos($entityNamespace, '\\Entity'));

        foreach ($this->kernel->getBundles() as $type => $bundle) {
            $bundleRefClass = new \ReflectionClass($bundle);
            if ($bundleRefClass->getNamespaceName() === $dataBaseNamespace) {
                return '@' . preg_replace('/Bundle$/', '', $type);
            }
        }

        return null;
    }


    /**
     * get the default template path if no templates are configured
     *
     * @param NodeInterface $node
     * @return string
     * @throws \ReflectionException
     */
    public function getDefaultTemplate(NodeInterface $node)
    {
        $reflection = new \ReflectionClass(get_class($node));
        $name = $reflection->getShortName();
        $namespace = $reflection->getNamespaceName();
        $bundlePrefix = $this->getBundlePrefixFromEntity($namespace);

        $entityNsString = '\\Entity';
        $entityNsStringPos = strpos($namespace, $entityNsString);

        $entityNamespace = substr($namespace, $entityNsStringPos + strlen($entityNsString)) ?: '';
        if ($entityNamespace)
            $entityNamespace .= "/";

        return $bundlePrefix . '/cmf/' . $entityNamespace . $name . '/' . $name . '_default.html.twig';
    }


    /**
     * Search for Templates which are locally overwritten
     *
     * @param $templatePath
     * @param $node
     * @return string|null
     */
    protected function resolveLocalTemplate($templatePath, $node)
    {
        $nodeMeta = $this->factory->getNodeMeta($node);
        $tags = $nodeMeta->getTags();

        foreach (array_reverse($tags) as $tag) {

            if (preg_match('/^(@[a-zA-Z0-9_]+\/|[a-zA-Z0-9_]+:)(.+)/', $templatePath, $matches)) {
                $bundle = $matches[1];
                $path = $matches[2];

                $localTemplateTmp = $bundle . $this->overrideDir . '/' . $tag . '/' . $path;

                if ($this->twig->exists($localTemplateTmp)) {
                    $templatePath = $localTemplateTmp;
                    break;
                }
            }
        }

        return $templatePath;
    }
}