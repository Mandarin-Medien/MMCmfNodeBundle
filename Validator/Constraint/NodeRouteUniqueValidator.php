<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
            ->select('r')
            ->from($this->repositoryClass, 'r')
            ->where("r.route = :routeUri")
            ->setParameter(':routeUri', $nodeRoute->getRoute());

        if ($nodeRoute->getId()) {
            $qb
                ->andWhere('r.id != :id')
                ->setParameter(':id', $nodeRoute->getId());
        }

        $nodeRoutes = $qb->getQuery()->getScalarResult();

        if (count($nodeRoute->getDomains()) > 0)
            $nodeRoutes = array_filter($nodeRoutes, function ($routeData) use ($nodeRoute) {

                $domains = $routeData['r_domains'];
                $match = array_intersect($nodeRoute->getDomains(), $domains);

                return (bool)count($match);
            });

        if (count($nodeRoutes) > 0) {
            foreach ($nodeRoutes as $route) {
                $nodeRouteEditLink = $this->router->generate('mm_admin_content_extension_route_edit', ['id' => $route['r_id']]);

                $this->context->buildViolation($constraint->message)
                    ->atPath('route')
                    ->setParameter('%nodeRouteEditLink%', $nodeRouteEditLink)
                    ->setParameter('%string%', $nodeRoute->getRoute())
                    ->setParameter('%domains%', implode(', ', $route['r_domains']))
                    ->setParameter('%routeId%', $route['r_id'])
                    ->addViolation();
            }
        }
    }
}