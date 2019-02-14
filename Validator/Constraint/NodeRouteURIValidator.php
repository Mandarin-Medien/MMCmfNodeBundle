<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NodeRouteURIValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $pattern = '/^\/(.*)$/';

        if(!preg_match($pattern, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}