<?php

namespace MandarinMedien\MMCmfNodeBundle\Controller;

use MandarinMedien\MMCmfNodeBundle\Entity\AliasNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\RedirectNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Event\NodeEvent;
use MandarinMedien\MMCmfNodeBundle\Event\NodeEvents;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeResolver;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeController extends Controller
{
    /**
     * process the the node route call
     * @param Request $request
     * @param NodeRoute $nodeRoute
     * @param EventDispatcherInterface $dispatcher
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function nodeRouteAction(Request $request, NodeRoute $nodeRoute, EventDispatcherInterface $dispatcher)
    {

        // before resolving &$request, &$nodeRoute
        $dispatcher->dispatch(NodeEvents::BEFORE_RESOLVING, new NodeEvent($request, $nodeRoute));

        if ($node = $this->get(NodeResolver::class)->resolve($nodeRoute)) {

            // after resolving &$request, &$node, &$nodeRoute

            if ($nodeRoute instanceof RedirectNodeRoute) {

                // before redirect &$request, &$node, &$nodeRoute,

                return $this->redirectAction($nodeRoute);
            } else {

                if ($node instanceof TemplatableNodeInterface) {

                    $templateFile = $this->get(TemplateManager::class)->getTemplate($node);
                    $templateData = array(
                        'node' => $node,
                        'route' => $nodeRoute
                    );
                    // before render - &$request, &$node, &$nodeRoute, &$templateFile, &$templateData

                    $response = $this->render($templateFile, $templateData);

                    // after render - &$request, &$node, &$nodeRoute, $templateFile, $templateData

                    /**
                     * should be configurable
                     * cache for 1800 seconds
                     */
                    $response->setSharedMaxAge(1800);

                    // (optional) set a custom Cache-Control directive
                    $response->headers->addCacheControlDirective('must-revalidate', true);

                    // before response &$request, &$response, &$node, &$nodeRoute,

                    return $response;

                } else {

                    // before not found -  &$request, &$nodeRoute
                    $dispatcher->dispatch(NodeEvents::BEFORE_NOT_FOUND, new NodeEvent($request, $nodeRoute));

                    throw new NotFoundHttpException();
                }
            }
        } else {

            // before not found -  &$request, &$nodeRoute
            $dispatcher->dispatch(NodeEvents::BEFORE_NOT_FOUND, new NodeEvent($request, $nodeRoute));

            throw new NotFoundHttpException();
        }
    }


    /**
     *
     * default redirect action
     * triggered when a RedirectNodeRoute is called
     *
     * @TODO: Extend RedirectNodeRoute, so an target Route is selectable
     *
     * @param RedirectNodeRoute $nodeRoute
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectAction(RedirectNodeRoute $nodeRoute)
    {
        $status = $nodeRoute->getStatusCode();

        $nodeRouteResolver = $this->get(NodeResolver::class);
        $node = $nodeRouteResolver->resolve($nodeRoute);

        foreach ($node->getRoutes() as $route) {
            if ($route instanceof AutoNodeRoute) {
                return $this->redirectToRoute("mm_cmf_node", array(
                    'route' => trim($route->getRoute(), '/')
                ), $status);
            }
        }

        foreach ($node->getRoutes() as $route) {
            if ($route instanceof AliasNodeRoute) {
                return $this->redirectToRoute("mm_cmf_node", array(
                    'route' => trim($route->getRoute(), '/')
                ), $status);
            }
        }
    }
}
