<?php


namespace App\Service;

use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapService
{

    private $client;


    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getMapInfo($countryCode): array
    {

        $response = $this->client->request(
            'GET',
            'http://api.worldbank.org/v2/country/'.$countryCode.'?format=json'
        );

        return $response->toArray();
    }

}