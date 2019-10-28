<?php

namespace MandarinMedien\MMCmfNodeBundle\Request;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeRouteResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class NodeRouteParamConverter implements ParamConverterInterface
{
    /**
     * @var NodeRouteResolver
     */
    private $nodeRouteResolver;


    private $redirectTrailingSlash = false;
    /**
     * @var RouterInterface
     */
    private $router;


    public function __construct(NodeRouteResolver $nodeRouteResolver, RouterInterface $router)
    {
        $this->nodeRouteResolver = $nodeRouteResolver;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    function apply(Request $request, ParamConverter $configuration)
    {
        if ($request->get('_route') !== "mm_cmf_node")
            return false;

        $domain = null;
        if ($request)
            $domain = $request->getHost();

        $routeUri = $request->attributes->get('route');

        if ($domain !== null && $routeUri !== null && !is_null($route = $this->nodeRouteResolver->getNodeRoute($routeUri, $domain))) {

            $request->attributes->add(
                array($configuration->getName() => $route)
            );
            return true;
        }

        /**
         *
         * if Route is not Found and the url contains a trailing slash
         * send a RedirectResponse to same URL without Trailing slash
         */
        if($this->getRedirectTrailingSlash() && preg_match('/^(.+)\/$/',$request->getPathInfo()))
        {
            $url = preg_replace('/\/$/', '', $this->router->getContext()->getBaseUrl().$request->getPathInfo());
            $response = new RedirectResponse($url, 301);
            $response->send();
            die();
        }



        throw new NotFoundHttpException('Route ' . $routeUri . ' not found.');
    }

    /**
     * @return bool
     */
    public function getRedirectTrailingSlash(): bool
    {
        return $this->redirectTrailingSlash;
    }

    /**
     * @param bool $redirectTrailingSlash
     * @return NodeRouteParamConverter
     */
    public function setRedirectTrailingSlash(bool $redirectTrailingSlash)
    {
        $this->redirectTrailingSlash = $redirectTrailingSlash;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == NodeRoute::class;
    }
}