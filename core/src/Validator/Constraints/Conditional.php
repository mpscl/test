<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Composite;

class Conditional extends Composite
{
    /**
     * Constraint list to apply.
     *
     * @var array
     */
    public $constraints = [];

    /**
     * Function or callable to indicates if constraints must be validated or not.
     *
     * @var callable
     */
    public $condition;

    /**
     * Indicates where the condition return value must be true or false.
     *
     * @var boolean
     */
    public $mustMatch = true;

    /**
     * @return string
     */
    public function getDefaultOption(): string
    {
        return 'constraints';
    }

    /**
     * @return array
     */
    public function getRequiredOptions(): array
    {
        return ['constraints', 'condition'];
    }

    /**
     * @return string
     */
    protected function getCompositeOption(): string
    {
        return 'constraints';
    }

    /**
     * @return array
     */
    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT, self::CLASS_CONSTRAINT];
    }
}
