<?php

namespace MandarinMedien\MMCmfNodeBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\ContentNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;
use Symfony\Component\HttpKernel\KernelInterface;


class MMCmfNodeTwigExtension extends \Twig_Extension
{

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;


    /**
     * @var TemplateManager
     */
    protected $templateManager;

    /**
     * @var KernelInterface
     */
    protected $kernel;


    public function __construct(NodeFactory $factory, TemplateManager $manager, KernelInterface $kernel)
    {
        $this->templateManager = $manager;
        $this->nodeFactory = $factory;
        $this->kernel = $kernel;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('class_discriminator', [$this->nodeFactory, 'getDiscriminatorByClassName'])
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('renderNode', [$this, "render"], [
                'is_safe' => ['html'],
                'needs_environment' => true
            ])
        ];
    }


    /**
     * @param \Twig_Environment $twig
     * @param TemplatableNodeInterface $node
     * @param string|null $template
     * @param array $options
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(\Twig_Environment $twig, TemplatableNodeInterface $node, string $template = null, array $options = [])
    {
        $twig->render($template ?: $this->templateManager->getTemplate($node), [
            'node' => $node
        ]);
    }

}