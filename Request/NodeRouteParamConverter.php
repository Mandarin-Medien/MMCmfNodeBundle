<?php

namespace MandarinMedien\MMCmfNodeBundle\Request;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;
use MandarinMedien\MMCmfNodeBundle\Resolver\NodeRouteResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeRouteParamConverter implements ParamConverterInterface
{
    /**
     * @var NodeRouteResolver
     */
    private $nodeRouteResolver;

    public function __construct(NodeRouteResolver $nodeRouteResolver)
    {
        $this->nodeRouteResolver = $nodeRouteResolver;
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

        throw new NotFoundHttpException('Route ' . $routeUri . ' not found.');
    }


    /**
     * {@inheritdoc}
     */
    function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == NodeRoute::class;
    }
}