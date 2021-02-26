<?php


declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class DataService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $queryParams
     * @return array
     * @throws TransportExceptionInterface
     */
    public function getDataFromApi(array $queryParams): array
    {

        $response = $this->client->request
        ('GET', 'https://api.stackexchange.com/2.2/questions?order=desc&sort=activity&tagged='.$queryParams['tag'].'&site=stackoverflow&filter=!5-dn)8xu6QZI)ZZw.Qkh3LnsZb7HOcBh(7WtBX');

        try {
            return $response->toArray();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface | DecodingExceptionInterface $e) {
            throw new ConflictHttpException('Service down');
        }
    }
}
