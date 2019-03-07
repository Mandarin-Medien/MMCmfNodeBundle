<?php

namespace MandarinMedien\MMCmfNodeBundle\Request;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NodeRouteParamConverter implements ParamConverterInterface
{

    private $manager;
    private $repositoryClass = NodeRoute::class;
    private $routeParamName = 'route';

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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

        if ($domain !== null && $routeUri !== null && !is_null($route = $this->getNodeRoute($routeUri, $domain))) {

            $request->attributes->add(
                array($configuration->getName() => $route)
            );
            return true;
        }

        throw new NotFoundHttpException('Route ' . $routeUri . ' not found.');
    }

    function getNodeRoute($uri, $domain = null)
    {
        $routeUri = (strpos($uri, '/') === 0) ? $uri : '/' . $uri;

        $qb = $this->manager
            ->createQueryBuilder()
            ->select('nodeRoute, domain')
            ->from($this->repositoryClass, 'nodeRoute')
            ->where('nodeRoute.route = :route')
            ->setMaxResults(1)
            ->setParameter(':route', $routeUri);

        if ($domain) {
            $qb
                ->leftJoin('nodeRoute.domains', 'domain')
                ->andWhere(' domain is NULL OR domain.name = :domain')
                ->setParameter(':domain', $domain);
        }

        $node = $qb->getQuery()->getResult();

        return count($node) ? $node[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == $this->repositoryClass;
    }
}