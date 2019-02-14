<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NodeRouteURI extends Constraint
{
    public $message  = 'The route "%string%" must start with a slash.';
}