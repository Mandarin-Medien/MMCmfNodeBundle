<?php

namespace MandarinMedien\MMCmfNodeBundle\Twig;

use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\ContentNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;


class MMCmfNodeRenderExtension extends \Twig_Extension
{

    /**
     * @var TemplateManager
     */
    protected $templateManager;


    protected $manager;


    protected $tokenStorage;


    public function __construct(TemplateManager $templateManager)
    {
        $this->templateManager = $templateManager;
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