<?php


namespace App\EventListener;

use App\Exceptions\ValidationException;
use App\Helpers\AppHelper;
use App\Traits\ApiResponse;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Exception\ConnectionException AS DBALConnectionException;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    use ApiResponse;

    private $_doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->_doctrine = $doctrine;
    }

    /**
     * Detector de excepciones personalizado
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if($exception instanceof ValidationException){
            return $this->getResponseValidatorException($event, $exception);
        }

        $code = $exception->getCode();

        if(method_exists($exception, 'getStatusCode')){
            $code = $exception->getStatusCode();
        }

        $code = $code > 0 ? $code : Response::HTTP_INTERNAL_SERVER_ERROR;

        $message = $exception->getMessage();

        return $event->setResponse($this->errorResponse(['code' => $code, 'message' => $message], $code));
    }

    /**
     * Obtener respuesta para errores de valicación de campos
     *
     * @param ExceptionEvent $event
     * @param ValidationException $exception
     */
    public function getResponseValidatorException(ExceptionEvent $event, ValidationException $exception){
        $code = $exception->getCode();
        $errors = $exception->getErrors();

        return $event->setResponse($this->errorResponse(['errorFields' => $errors], $code));
    }



}