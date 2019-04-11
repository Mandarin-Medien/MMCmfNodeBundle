<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-07
 * Time: 15:16
 */

namespace MandarinMedien\MMCmfNodeBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\LanguageNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Factory\NodeFactory;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeResolver;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeRouteResolver;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\TranslatorInterface;

class LanguageNodeInterfaceSubscriber implements EventSubscriberInterface
{
    const NODE_ROUTE_NAME = "mm_cmf_node";

    /**
     * @var NodeFactory
     */
    private $factory;

    /**
     * @var NodeRouteResolver
     */
    private $nodeRouteResolver;
    /**
     * @var NodeResolver
     */
    private $nodeResolver;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * LanguageNodeInterfaceSubscriber constructor.
     * @param NodeFactory $factory
     * @param NodeRouteResolver $nodeRouteResolver
     * @param NodeResolver $nodeResolver
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(NodeFactory $factory, NodeRouteResolver $nodeRouteResolver, NodeResolver $nodeResolver, TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->factory = $factory;
        $this->nodeRouteResolver = $nodeRouteResolver;
        $this->nodeResolver = $nodeResolver;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param GetResponseEvent $event
     * @throws \ReflectionException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $event->getRequest()->get('_route');

        if ($routeName && $routeName === self::NODE_ROUTE_NAME) {

            $domain = null;

            if ($request)
                $domain = $request->getHost();

            $routeUri = $request->attributes->get('route');

            $route = $this->nodeRouteResolver->getNodeRoute($routeUri, $domain);
            if ($route && $node = $this->nodeResolver->resolve($route)) {

                if ($nodeMeta = $this->factory->getNodeMeta($node)) {
                    while ($nodeMeta->getParent() !== null)
                        if ((new \ReflectionClass($nodeMeta->getClassname()))->implementsInterface(LanguageNodeInterface::class))
                            break;
                        else
                            $nodeMeta = $nodeMeta->getParent();

                    /**
                     * @var $languageNode LanguageNodeInterface
                     */
                    if ((new \ReflectionClass($nodeMeta->getClassname()))->implementsInterface(LanguageNodeInterface::class)) {
                        $languageNode = $this->entityManager->find($nodeMeta->getClassname(), $nodeMeta->getId());
                        if ($languageNode && $locale = $languageNode->getLocale()) {
                            $request->setLocale($locale);
                            $this->translator->setLocale($locale);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }
}