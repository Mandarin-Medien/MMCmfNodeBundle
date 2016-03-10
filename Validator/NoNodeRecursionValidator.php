<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator;

use Doctrine\ORM\EntityManager;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NoNodeRecursionValidator extends ConstraintValidator
{
    private $repositoryClass = Node::class;

    /**
     * @var EntityManager
     */
    private $manager;

    public function validate($node, Constraint $constraint)
    {
        if($this->inTree($node, $node->getParent())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $node->getName())
                ->addViolation();
        }
    }


    protected function inTree(NodeInterface $node1, NodeInterface $node2)
    {
        if($node1 == $node2) {
            return true;
        } elseif(!is_null($parent = $node1->getParent())) {
            return $this->inTree($parent, $node2);
        }

        return false;
    }


    public function setManager(EntityManager $manager)
    {
        $this->manager = $manager;
    }
}