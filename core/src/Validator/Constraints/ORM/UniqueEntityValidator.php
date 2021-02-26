<?php
declare(strict_types=1);

namespace App\Validator\Constraints\ORM;

use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends UniqueOrExistsConstraintValidator
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    /**
     * Constraint validate.
     *
     * @param mixed $value
     * @param Constraint $constraint
     * @throws Exception
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        $this->initialValidations($constraint);

        $criteria = [$constraint->defaultColumn => $value];
        $hasNullValue = false;

        foreach ($constraint->conditions as $fieldName => $fieldValue) {
            if (!$this->classMetadata->hasField($fieldName) && !$this->classMetadata->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.', $fieldName));
            }

            if (null === $fieldValue) {
                $hasNullValue = true;
            }

            if ($constraint->ignoreNull && null === $fieldValue) {
                continue;
            }

            $criteria[$fieldName] = $value;

            if (null !== $criteria[$fieldName] && $this->classMetadata->hasAssociation($fieldName)) {
                /* Ensure the Proxy is initialized before using reflection to
                 * read its identifiers. This is necessary because the wrapped
                 * getter methods in the Proxy are being bypassed.
                 */
                $this->em->initializeObject($criteria[$fieldName]);
            }
        }

        // validation doesn't fail if one of the fields is null and if null values should be ignored
        if ($hasNullValue && $constraint->ignoreNull) {
            return;
        }

        // skip validation if there are no criteria (this can happen when the
        // "ignoreNull" option is enabled and fields to be checked are null
        if (empty($criteria)) {
            return;
        }

        $result = $this->getDataFromRepository($constraint, $criteria);

        /* If no entity matched the query criteria or a single entity matched,
         * which is the same as the entity being validated, the criteria is
         * unique.
         */
        if (!$result || (1 === \count($result) && current($result) === $this->entity)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($value))
            ->addViolation();
    }
}