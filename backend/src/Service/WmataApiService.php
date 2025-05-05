<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WmataApiService
{
    private $httpClient;
    private $params;
    private $apiBaseUrl;
    private $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $params
    ) {
        $this->httpClient = $httpClient;
        $this->params = $params;
        $this->apiBaseUrl = $this->params->get('app.wmata_api.base_url');
        $this->apiKey = $this->params->get('app.wmata_api.api_key');
    }

    public function getNextTrains(string $stationId): array
    {
        $response = $this->httpClient->request('GET', "{$this->apiBaseUrl}/StationPrediction.svc/json/GetPrediction/{$stationId}", [
            'headers' => [
                'api_key' => "{$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }

    public function getStations(): array
    {
        $response = $this->httpClient->request('GET', "{$this->apiBaseUrl}/Rail.svc/json/jStations", [
            'headers' => [
                'api_key' => "{$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return $response->toArray();
    }
}
