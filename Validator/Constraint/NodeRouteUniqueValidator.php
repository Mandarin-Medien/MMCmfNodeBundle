<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NodeRouteUniqueValidator extends ConstraintValidator
{
    private $repositoryClass = NodeRoute::class;

    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $manager, RouterInterface $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    /**
     * @param NodeRoute $nodeRoute
     * @param Constraint $constraint
     */
    public function validate($nodeRoute, Constraint $constraint)
    {


        /**
         * @var NodeRoute $nodeRoute
         */
        $qb = $this->manager->createQueryBuilder()
            ->select('r, d')
            ->from($this->repositoryClass, 'r')
            ->leftJoin('r.domains', 'd')
            ->where("r.route = :routeUri")
            ->setParameter(':routeUri', $nodeRoute->getRoute());

        if ($nodeRoute->getId()) {
            $qb
                ->andWhere('r.id != :id')
                ->setParameter(':id', $nodeRoute->getId());
        }

        $nodeRoutes = $qb->getQuery()->getResult();

        if (count($nodeRoute->getDomains()) > 0)
            $nodeRoutes = array_filter($nodeRoutes, function (NodeRoute $_nodeRoute) use ($nodeRoute) {

                $match = array_intersect(
                    $nodeRoute->getDomains()->toArray(),
                    $_nodeRoute->getDomains()->toArray()
                );

                return (bool)count($match);
            });

        if (count($nodeRoutes) > 0) {
            foreach ($nodeRoutes as $route) {
                $nodeRouteEditLink = $this->router->generate('mm_admin_content_extension_route_edit', ['id' => $route->getId()]);

                $this->context->buildViolation($constraint->message)
                    ->atPath('route')
                    ->setParameter('%nodeRouteEditLink%', $nodeRouteEditLink)
                    ->setParameter('%string%', $nodeRoute->getRoute())
                    ->setParameter('%domains%', implode(', ', $route->getDomains()->toArray()))
                    ->setParameter('%routeId%', $route->getId())
                    ->addViolation();
            }
        }
    }
}