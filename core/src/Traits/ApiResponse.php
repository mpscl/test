<?php
declare(strict_types=1);

namespace App\Traits;

use App\Helpers\AppHelper;
use App\Helpers\SerializerHelper;
use App\Serializer\Exclusion\DepthExclusionStrategy;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{

    /**
     * Format json response.
     *
     * @param $data
     * @param int $code
     * @return JsonResponse
     */
    public function formatResponse($data = null, int $code = Response::HTTP_OK)
    {
        return $this->json($data, $code);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    protected function json(
        $data,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $context = []
    ): JsonResponse {
        return new JsonResponse($this->jmsSerializer($data), $status, $headers);
    }

    /**
     * Serializar data usando JMS Serializer.
     *
     * @param $data
     * @return mixed
     */
    private function jmsSerializer($data)
    {
        $container = AppHelper::getContainerInterface();

        if ($container->has('jms_serializer')) {
            $context = $this->createSerializationContext();
            $json = $container->get('jms_serializer')->serialize($data, 'json', $context);

            $data = json_decode($json, true);
        }

        return $data;
    }

    /**
     * Create serialization context.
     *
     * @return SerializationContext
     */
    private function createSerializationContext(): SerializationContext
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $context->addExclusionStrategy(
            new DepthExclusionStrategy(SerializerHelper::getDepth())
        );

        return $context;
    }
}