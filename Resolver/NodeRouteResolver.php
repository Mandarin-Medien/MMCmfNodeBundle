<?php
/**
 * Created by PhpStorm.
 * User: tonigurski
 * Date: 2019-03-13
 * Time: 14:28
 */

namespace MandarinMedien\MMCmfNodeBundle\Resolver;


use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRouteInterface;

class NodeRouteResolver
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $repositoryClass = NodeRoute::class;

    /**
     * NodeRouteResolver constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $uri
     * @param null $domain
     * @return null|NodeRouteInterface
     */
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
}