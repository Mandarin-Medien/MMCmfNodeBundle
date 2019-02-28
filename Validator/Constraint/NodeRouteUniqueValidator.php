<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeRoute;
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

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $nodeRoute->getRoute())
                ->setParameter('%routeId%', $nodeRoutes[0]['r_id'])
                ->addViolation();
        }
    }
}