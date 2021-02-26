<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Exceptions\ValidationException;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class JsonExceptionResponseTransformerListener
{
    use ApiResponse;

    /* @var ExceptionEvent $event */
    protected $exceptionEvent;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onKernelException(ExceptionEvent $event){
        $this->exceptionEvent = $event;

        $exception = $event->getThrowable();

        if($exception instanceof ValidationException){
            return $this->getEventExceptionResponse(
                $exception->getMessage(),
                $exception->getCode(),
                ['errors' => $exception->getErrors()]
            );
        }

        if($exception instanceof NotFoundHttpException){
            return $this->getNotFoundHttpExceptionResponse($exception);
        }

        if($exception instanceof HttpExceptionInterface){
            return $this->getEventExceptionResponse($exception->getMessage(), $exception->getStatusCode());
        }

        try {
            return $this->getEventExceptionResponse($exception->getMessage(), $exception->getCode());
        }catch (\Exception $exc){
            return $this->getEventExceptionResponse(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get json response for "HttpNotFoundException" type exceptions. In addition, it handles responses
     * for when there is no information from the requested entity.
     *
     * @param NotFoundHttpException $exception
     * @return void
     */
    private function getNotFoundHttpExceptionResponse(NotFoundHttpException $exception){
        $message = $exception->getMessage();

        if(strpos($message, 'object not found by the @ParamConverter annotation') !== FALSE){
            list($entity) = explode(" ", $message);

            $entity = substr(strrchr($entity, "\\"), 1);

            $message = sprintf($this->translator->trans('http_status_code.not_found_entity'), $entity);
        }

        return $this->getEventExceptionResponse($message, $exception->getStatusCode());
    }

    /**
     * Resolve json response for exceptions.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $extras
     */
    private function getEventExceptionResponse(string $message, int $statusCode, array $extras = []): void{
        $data = [
            'message' => $message
        ];

        $data = array_merge($data, $extras);

        $json = $this->formatResponse($data, $statusCode);

        $this->exceptionEvent->setResponse($json);
    }
}