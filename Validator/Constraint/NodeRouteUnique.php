<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NodeRouteUnique extends Constraint
{
    public $message  = 'The route "%string%" is not unique. Look at Route ID "%routeId%"';

    public function validatedBy()
    {
        return 'node_route_unique';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}