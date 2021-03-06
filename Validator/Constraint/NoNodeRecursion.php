<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoNodeRecursion extends Constraint
{
    public $message  = '%string% darf sich nicht selber beinhalten';


    public function validatedBy()
    {
        return 'no_node_recursion';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}