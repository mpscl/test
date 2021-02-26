<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AllowedFieldsToFilterValidator extends ConstraintValidator
{
    public function validate($filters, Constraint $constraint)
    {
        $allowedFilters = $constraint->payload;

        if(!$filters || !is_array($filters)){
            return;
        }





//        if(!is_array($filters)){
//            return $this->context->buildViolation('El campo debe ser un array')
//                ->setParameter('%string%', implode(', ', $allowedFilters))
//                ->addViolation();
//        }

        if(count($filters) === 0 || !is_array($allowedFilters)){
            return;
        }

        foreach ($filters as $key => $value){
            if(!in_array($key, $allowedFilters)){

                return $this->context->buildViolation($constraint->message)
                            ->setParameter('%string%', implode(', ', $allowedFilters))
                            ->addViolation();
            }
        }

    }
}