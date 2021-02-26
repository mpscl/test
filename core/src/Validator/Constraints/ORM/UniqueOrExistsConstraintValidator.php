<?php
declare(strict_types=1);

namespace App\Validator\Constraints\ORM;

use Exception;
use Traversable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueOrExistsConstraintValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    protected $classMetadata;

    protected $entity;

    /**
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }

    /**
     * Initial validations.
     *
     * @param $constraint
     * @return void
     */
    protected function initialValidations($constraint): void
    {
        $this->em = $this->registry->getManagerForClass($constraint->entityClass);

        if (!$this->em) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'Unable to find the object manager associated with an entity of class "%s".',
                    $constraint->entityClass
                )
            );
        }

        $this->entity = new $constraint->entityClass();

        $this->classMetadata = $this->em->getClassMetadata($constraint->entityClass);

        $this->repository = $this->em->getRepository($constraint->entityClass);

        $supportedClass = $this->repository->getClassName();

        if (!$this->entity instanceof $supportedClass) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'The "%s" entity repository does not support the "%s" entity. The entity should be an instance of or extend "%s".',
                    $constraint->entityClass,
                    $this->classMetadata->getName(),
                    $supportedClass
                )
            );
        }

        if (!\is_array($constraint->conditions) && !\is_string($constraint->conditions)) {
            throw new UnexpectedTypeException($constraint->conditions, 'array');
        }
    }

    /**
     * Get data from repository by criteria.
     *
     * @param $constraint
     * @param array $criteria
     * @return array|Traversable
     * @throws Exception
     */
    protected function getDataFromRepository($constraint, array $criteria)
    {
        $result = $this->repository->{$constraint->repositoryMethod}($criteria);

        if ($result instanceof \IteratorAggregate) {
            $result = $result->getIterator();
        }

        /* If the result is a MongoCursor, it must be advanced to the first
         * element. Rewinding should have no ill effect if $result is another
         * iterator implementation.
         */
        if ($result instanceof \Iterator) {
            $result->rewind();
            if ($result instanceof \Countable && 1 < \count($result)) {
                $result = [$result->current(), $result->current()];
            } else {
                $result = $result->current();
                $result = null === $result ? [] : [$result];
            }
        } elseif (\is_array($result)) {
            reset($result);
        } else {
            $result = null === $result ? [] : [$result];
        }

        return $result;
    }

    protected function formatWithIdentifiers($value)
    {
        if (!\is_object($value) || $value instanceof \DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }

        if (method_exists($value, '__toString')) {
            return (string)$value;
        }

        if ($this->classMetadata->getName() !== $idClass = \get_class($value)) {
            // non unique value might be a composite PK that consists of other entity objects
            if ($this->em->getMetadataFactory()->hasMetadataFor($idClass)) {
                $identifiers = $this->em->getClassMetadata($idClass)->getIdentifierValues($value);
            } else {
                // this case might happen if the non unique column has a custom doctrine type and its value is an object
                // in which case we cannot get any identifiers for it
                $identifiers = [];
            }
        } else {
            $identifiers = $this->classMetadata->getIdentifierValues($value);
        }

        if (!$identifiers) {
            return sprintf('object("%s")', $idClass);
        }

        array_walk(
            $identifiers,
            function (&$id, $field) {
                if (!\is_object($id) || $id instanceof \DateTimeInterface) {
                    $idAsString = $this->formatValue($id, self::PRETTY_DATE);
                } else {
                    $idAsString = sprintf('object("%s")', \get_class($id));
                }

                $id = sprintf('%s => %s', $field, $idAsString);
            }
        );

        return sprintf('object("%s") identified by (%s)', $idClass, implode(', ', $identifiers));
    }
}