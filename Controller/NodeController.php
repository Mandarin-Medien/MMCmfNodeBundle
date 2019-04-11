<?php

namespace MandarinMedien\MMCmfNodeBundle\Controller;

use MandarinMedien\MMCmfNodeBundle\Entity\AliasNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\RedirectNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerEvent;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerEvents;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerWithNodeEvent;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerWithResponseEvent;
use MandarinMedien\MMCmfNodeBundle\Event\NodeControllerWithTemplateDataEvent;
use MandarinMedien\MMCmfNodeBundle\Exception\NoTemplatableNodeFoundException;
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
        $dispatcher->dispatch(NodeControllerEvents::BEFORE_RESOLVING, new NodeControllerEvent($request, $nodeRoute));

        if ($node = $this->get(NodeResolver::class)->resolve($nodeRoute)) {

            // after resolving &$request, &$node, &$nodeRoute
            $dispatcher->dispatch(NodeControllerEvents::AFTER_RESOLVING, new NodeControllerWithNodeEvent($request, $nodeRoute, $node));

            if ($nodeRoute instanceof RedirectNodeRoute) {

                // before redirect &$request, &$node, &$nodeRoute,
                $dispatcher->dispatch(NodeControllerEvents::BEFORE_REDIRECT, new NodeControllerWithNodeEvent($request, $nodeRoute, $node));

                return $this->redirectAction($nodeRoute);
            } else {

                if ($node instanceof TemplatableNodeInterface) {

                    $templateFile = $this->get(TemplateManager::class)->getTemplate($node);
                    $templateData = array(
                        'node' => $node,
                        'route' => $nodeRoute
                    );
                    // before render - &$request, &$node, &$nodeRoute, &$templateData, &$templateFile
                    $dispatcher->dispatch(NodeControllerEvents::BEFORE_RENDER, new NodeControllerWithTemplateDataEvent($request, $nodeRoute, $node, $templateData, $templateFile));

                    /**
                     * render response
                     */
                    $response = $this->render($templateFile, $templateData);

                    // after render - &$request, &$node, &$nodeRoute, $templateFile, $templateData
                    $dispatcher->dispatch(NodeControllerEvents::AFTER_RENDER, new NodeControllerWithResponseEvent($request, $nodeRoute, $response));

                    // before response &$request, &$response, &$node, &$nodeRoute,
                    $dispatcher->dispatch(NodeControllerEvents::BEFORE_RESPONSE, new NodeControllerWithResponseEvent($request, $nodeRoute, $response));

                    return $response;

                } else {

                    // before not found -  &$request, &$nodeRoute
                    $dispatcher->dispatch(NodeControllerEvents::BEFORE_NOT_FOUND, new NodeControllerWithNodeEvent($request, $nodeRoute, $node));

                    throw new NoTemplatableNodeFoundException(null, null, 0, $node, $nodeRoute);
                }
            }
        } else {

            // before not found -  &$request, &$nodeRoute
            $dispatcher->dispatch(NodeControllerEvents::BEFORE_NOT_FOUND, new NodeControllerEvent($request, $nodeRoute));

            throw new NotFoundHttpException(sprintf('The route %s was found but the node could not get resolved. Either the Route (#%s) is an orphan or the node is not visible.', $nodeRoute->getRoute(), $nodeRoute->getId()));
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
