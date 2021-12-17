<?php


namespace App\Service;




use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{

    private $client;


    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getSurahData(int $id_sourate): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.alquran.cloud/v1/surah/'.$id_sourate
        );

        return $response->toArray();
    }

    public function getPageData(int $id_page): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.alquran.cloud/v1/page/'.$id_page.'/quran-uthmani'
        );

        return $response->toArray();
    }

}