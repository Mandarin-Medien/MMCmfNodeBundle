<?php

namespace MandarinMedien\MMCmfNodeBundle\Twig;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\ContentNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;


class MMCmfNodeTwigExtension extends \Twig_Extension
{

    /**
     * @var TemplateManager
     */
    protected $templateManager;


    /**
     * @var NodeFactory
     */
    protected $nodeFactory;


    public function __construct(NodeFactory $factory, TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
        $this->nodeFactory = $factory;
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

        if(     $node instanceof NodeInterface
            &&  $node instanceof ContentNodeInterface
        ) {

            $template = $template ?: $this->templateManager->getTemplate($node);

            return $twig->render($template, ['node' => $node]);
        }

        return '';
    }
}