<?php

namespace MandarinMedien\MMCmfNodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class RedirectStatusCodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        if(!is_null($value)) {
            $pattern = '/^30([1-8])$/';

            if (!preg_match($pattern, $value)) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}