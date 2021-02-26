<?php


namespace App\Exceptions;

use Exception;
use Throwable;
use App\Helpers\ValidationHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    /**
     * Error list.
     *
     * @var array $errors
     */
    protected $errors;

    public function __construct(
        ConstraintViolationListInterface $errors,
        int $code = Response::HTTP_BAD_REQUEST,
        string $message = "Validation failed",
        Throwable $previous = null
    ){
        $this->setErrors($errors);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array{
        return $this->errors;
    }

    /**
     * Assign and format validation errors.
     *
     * @param ConstraintViolationListInterface $errors
     */
    public function setErrors(ConstraintViolationListInterface $errors): void {
        $this->errors =  ValidationHelper::formatErrors($errors);
    }
}