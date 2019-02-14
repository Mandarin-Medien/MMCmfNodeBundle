<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NodeRouteUnique extends Constraint
{
    public $message  = 'The route "%string%" is not unique.';


    public function validatedBy()
    {
        return 'node_route_unique';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}