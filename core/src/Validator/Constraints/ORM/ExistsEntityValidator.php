<?php
declare(strict_types=1);

namespace App\Validator\Constraints\ORM;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistsEntityValidator extends UniqueOrExistsConstraintValidator
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry);

        $this->requestStack = $requestStack;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws Exception
     */
    public function validate($value, Constraint $constraint)
    {
        if(!$value){
            return;
        }

        if (!$constraint instanceof ExistsEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\CheckEntityExists');
        }

        $this->initialValidations($constraint);

        if (
            $constraint->mustTransformRequest &&
            !$constraint->propertyRequest &&
            !is_string($constraint->propertyRequest)
        ) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'The options "%s" must be set for constraint "%s"',
                    'propertyRequest',
                    get_class($constraint)
                )
            );
        }

        $criteria = [$constraint->defaultColumn => $value];

        foreach ($constraint->conditions as $fieldName => $value) {
            if (!$this->classMetadata->hasField($fieldName)) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        'The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.',
                        $fieldName
                    )
                );
            }

            $criteria[$fieldName] = $value;
        }

        $result = $this->getDataFromRepository($constraint, $criteria);

        if ($result || (1 === \count($result) && current($result) === $this->entity)) {
            $constraint->mustTransformRequest ? $this->requestStack->getCurrentRequest()->request->add(
                [$constraint->propertyRequest => current($result)]
            ) : false;

            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($value))
            ->addViolation();
    }
}