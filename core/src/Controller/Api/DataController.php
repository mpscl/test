<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\DataService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Exceptions\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;


class DataController extends ApiController
{
    /**
     * @var DataService
     */
    private $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Return data from stackoverflow using the api from stackexchange
     *
     * @Route("/get-data/", name="api_get_data", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function indexAction(Request $request): JsonResponse
    {
        $queryParams = $request->query->all();

        $constraints = [
            'tag' => [
                new Assert\NotBlank(),
            ],
        ];

        $this->validateDataRequest($queryParams, $constraints);
        $response = $this->dataService->getDataFromApi($queryParams);

        return $this->formatResponse($response);
    }
}
