<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RedirectStatusCode extends Constraint
{
    public $message  = 'The HTTP Status Code must be an valid Redirect Status Code';
}