<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConditionalValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Conditional) {
            throw new UnexpectedTypeException($constraint, Conditional::class);
        }

        $context = $this->context;

        if ($context instanceof ExecutionContextInterface) {
            $object = $this->context->getObject();
        } else {
            $object = $this->context->getRoot();
        }

        // execute callable condition with context object as parameter
        $condition = call_user_func($constraint->condition, $object);

        // if condition result match validate list of constraints
        if ($constraint->mustMatch !== $condition) {
            return;
        }

        if (!$context instanceof ExecutionContextInterface) {
            return;
        }

        $validator = $context->getValidator()->inContext($context);
        $validator->validate($value, $constraint->constraints, $this->context->getGroup());
    }
}
