<?php

namespace MandarinMedien\MMCmfNodeBundle\Twig;

use MandarinMedien\MMCmfNodeBundle\Entity\ContentNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;


class MMCmfNodeTwigExtension extends AbstractExtension
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

    /**
     * MMCmfNodeTwigExtension constructor.
     * @param NodeFactory $factory
     * @param TemplateManager $manager
     * @param KernelInterface $kernel
     */
    public function __construct(NodeFactory $factory, TemplateManager $manager, KernelInterface $kernel)
    {
        $this->templateManager = $manager;
        $this->nodeFactory = $factory;
        $this->kernel = $kernel;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('class_discriminator', [$this->nodeFactory, 'getDiscriminatorByClassName'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('renderNode', [$this, "render"], [
                'is_safe' => ['html'],
                'needs_environment' => true
            ]),
            new TwigFunction('renderNodeChildren', [$this, "renderChildren"], [
                'is_safe' => ['html'],
                'needs_environment' => true
            ])
        ];
    }


    /**
     * render children funtion
     *
     * this function is aware oft the class to render (default ContentNodeInterface)
     *
     * @param Environment $twig
     * @param NodeInterface $node
     * @param array $classes
     * @return string
     * @throws
     */
    public function renderChildren(Environment $twig, NodeInterface $node, array $classes = [ContentNodeInterface::class])
    {
        $content = '';

        foreach ($node->getNodes() as $childNode) {

            // test class instance
            foreach ($classes as $className)
                if (true === ($passed = ($node instanceof $className)))
                    break;

            // skip rendering when class is not allowed
            if (!$passed)
                return '';

            $content += $this->render($twig, $node);
        }

        return $content;

    }


    /**
     * render the given node
     *
     * @param \Twig_Environment $twig
     * @param TemplatableNodeInterface $node
     * @param string|null $template
     * @param array $options
     * @return string
     *
     * @throws
     */
    public function render(
        Environment $twig,
        TemplatableNodeInterface $node,
        string $template = null,
        array $options = []
    ) {


        $refClass = new \ReflectionClass($node);
        $className = trim(str_replace($refClass->getNamespaceName(), '', $refClass->getName()), '\\');
        $display_classes = [$className];

        return $twig->render($template ?: $this->templateManager->getTemplate($node), [
            'node' => $node,
            'display_classes' => implode(" ", $display_classes),
        ]);
    }
}