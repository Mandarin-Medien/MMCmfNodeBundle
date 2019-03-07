<?php

namespace MandarinMedien\MMCmfNodeBundle\Controller;

use MandarinMedien\MMCmfNodeBundle\Entity\AliasNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\AutoNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\RedirectNodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\TemplatableNodeInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeResolver;
use MandarinMedien\MMCmfNodeBundle\Templating\TemplateManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeController extends Controller
{
    /**
     * process the the node route call
     * @param NodeRoute $nodeRoute
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function nodeRouteAction(NodeRoute $nodeRoute)
    {

        if($node = $this->get(NodeResolver::class)->resolve($nodeRoute)) {

            if ($nodeRoute instanceof RedirectNodeRoute) {
                return $this->redirectAction($nodeRoute);
            } else {

                if ($node instanceof TemplatableNodeInterface) {

                    $response = $this->render(
                        $this->get(TemplateManager::class)->getTemplate($node),
                        array(
                            'node' => $node,
                            'route' => $nodeRoute
                        )
                    );

                    /**
                     * should be configurable
                     * cache for 1800 seconds
                     */
                    $response->setSharedMaxAge(1800);

                    // (optional) set a custom Cache-Control directive
                    $response->headers->addCacheControlDirective('must-revalidate', true);

                    return $response;

                } else {
                    throw new NotFoundHttpException();
                }
            }
        } else {
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
        $node =  $nodeRouteResolver->resolve($nodeRoute);

        foreach($node->getRoutes() as $route) {
            if($route instanceof AutoNodeRoute) {
                return $this->redirectToRoute("mm_cmf_node", array(
                    'route' => trim($route->getRoute(), '/')
                ), $status);
            }
        }

        foreach($node->getRoutes() as $route) {
            if($route instanceof AliasNodeRoute) {
                return $this->redirectToRoute("mm_cmf_node", array(
                    'route' => trim($route->getRoute(), '/')
                ), $status);
            }
        }
    }
}
