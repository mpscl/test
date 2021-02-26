<?php
declare(strict_types=1);

namespace App\Validator\Constraints\ORM;

use App\Helpers\AppHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;

class UniqueEntity extends Constraint
{
    public $message;
    public $em = null;
    public $entityClass = null;
    public $repositoryMethod = 'findBy';
    public $conditions = [];
    public $ignoreNull = true;
    public $defaultColumn = null;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->translator = AppHelper::getTranslatorInterface();

        $this->message = $this->translator->trans('validators.unique_entity');
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'defaultColumn'];
    }

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}