<?php

namespace App\Helpers;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationHelper
{
    /**
     * Aplicar formato a los errores de validación
     *
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    public static function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $output = [];

        foreach ($errors as $error) {
            $key = str_replace(array('[', ']'), '', $error->getPropertyPath());

            if(in_array($key, array_column($output, 'field'))){
                continue;
            }

            $output[] = array(
                'field' => $key,
                'message' => $error->getMessage()
            );
        }

        return $output;
    }
}