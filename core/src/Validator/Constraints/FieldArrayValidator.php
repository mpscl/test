<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FieldArrayValidator extends ConstraintValidator
{
    public function validate($filters, Constraint $constraint)
    {
        if(!$filters){
            return;
        }

        if(!is_array($filters)){
            return $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}