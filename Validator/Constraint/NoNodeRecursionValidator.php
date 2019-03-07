<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use MandarinMedien\MMCmfNodeBundle\Entity\NodeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NoNodeRecursionValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;


    /**
     * {@inheritdoc}
     */
    public function validate($node = null, Constraint $constraint)
    {
        /**
         * @var $node NodeInterface
         */
        if(is_null($node) || is_null($node->getParent())) return;

        if($this->inTree($node->getParent(), $node)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $node->getName())
                ->addViolation();
        }
    }


    /**
     * check the node tree for recursion
     *
     * @param NodeInterface $node1
     * @param NodeInterface $node2
     * @return bool
     */
    protected function inTree(NodeInterface $node1, NodeInterface $node2)
    {
        if($node1 == $node2) {
            return true;
        } elseif(!is_null($parent = $node1->getParent())) {
            return $this->inTree($parent, $node2);
        }

        return false;
    }


    /**
     * @param EntityManagerInterface $manager
     */
    public function setManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
}