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
     * @param mixed $nodeRoute
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
            ->where("r.route = ?1")
            ->andWhere('r.id != ?2')
            ->setParameter('1', $nodeRoute->getRoute())
            ->setParameter("2", $nodeRoute->getId())
            ->setMaxResults(1);

        $existing = $qb->getQuery()->execute();

        if(count($existing)>0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $nodeRoute->getRoute())
                ->addViolation();
        }
    }
}