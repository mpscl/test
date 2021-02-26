<?php
declare(strict_types=1);

namespace App\Validator\Constraints\ORM;

use App\Helpers\AppHelper;
use App\Helpers\TextHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExistsEntity extends Constraint
{
    public $message;
    public $entityClass = null;
    public $repositoryMethod = 'findBy';
    public $defaultColumn = 'id';
    public $conditions = [];
    public $mustTransformRequest = true;
    public $propertyRequest = null;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->translator = AppHelper::getTranslatorInterface();

        $this->message = $this->translator->trans('validators.exists_entity');

        $this->propertyRequest = $this->getPropertyRequest();
    }

    /**
     * @return array
     */
    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return get_class($this) . 'Validator';
    }

    /**
     * @return string
     */
    private function getPropertyRequest(): string {
        if($this->propertyRequest){
            return $this->propertyRequest;
        }

        return TextHelper::convertCamelCaseToSnakeCase(substr(strrchr($this->entityClass, "\\"), 1));
    }
}