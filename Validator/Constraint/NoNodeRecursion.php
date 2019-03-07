<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NoNodeRecursion extends Constraint
{
    public $message  = '%string% darf sich nicht selber beinhalten';


    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}