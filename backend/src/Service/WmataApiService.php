<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class WmataApiService
{
    private $httpClient;
    private $params;
    private $apiBaseUrl;
    private $apiKey;
    private $cache;
    
    private const MAX_RETRIES = 3;
    private const CACHE_TTL_STATIONS = 86400; // 24 hours for stations data
    private const RATE_LIMIT_PER_SECOND = 10;
    private const RATE_LIMIT_PER_DAY = 50000;
    
    // Track request counts for rate limiting
    private static $requestCount = 0;
    private static $lastRequestTime = 0;
    private static $dailyRequestCount = 0;
    private static $dailyRequestReset = null;
    
    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $params,
        CacheInterface $cache
    ) {
        $this->httpClient = $httpClient;
        $this->params = $params;
        $this->cache = $cache;
        $this->apiBaseUrl = $this->params->get('app.wmata_api.base_url');
        $this->apiKey = $this->params->get('app.wmata_api.api_key');
        
        if (self::$dailyRequestReset === null) {
            self::$dailyRequestReset = strtotime('tomorrow midnight');
        }
    }
    
    /**
     * Get next train predictions for a station
     * 
     * @param string $stationId
     * @return array
     */
    public function getNextTrains(string $stationId): array
    {
        $endpoint = "/StationPrediction.svc/json/GetPrediction/{$stationId}";
        
        // Train predictions should never be cached as they're real-time data
        return $this->makeApiRequest($endpoint);
    }
    
    /**
     * Get all stations with caching
     * 
     * @return array
     */
    public function getStations(): array
    {
        $cacheKey = 'wmata_stations';
        
        // Use Symfony's cache interface with a callback
        return $this->cache->get($cacheKey, function() {
            $endpoint = "/Rail.svc/json/jStations";
            return $this->makeApiRequest($endpoint);
        }, self::CACHE_TTL_STATIONS);
    }
    
    /**
     * Make API request with retry logic and rate limiting
     * 
     * @param string $endpoint
     * @param array $options
     * @param int $retry
     * @return array
     * @throws \Exception
     */
    private function makeApiRequest(string $endpoint, array $options = [], int $retry = 0): array
    {
        $now = time();
        if ($now >= self::$dailyRequestReset) {
            self::$dailyRequestCount = 0;
            self::$dailyRequestReset = strtotime('tomorrow midnight');
        }
        
        if (self::$dailyRequestCount >= self::RATE_LIMIT_PER_DAY) {
            throw new \Exception('WMATA API daily rate limit reached. Try again tomorrow.');
        }
        
        $currentSecond = floor($now);
        if ($currentSecond === self::$lastRequestTime) {
            if (self::$requestCount >= self::RATE_LIMIT_PER_SECOND) {
                $sleepTime = 1 - ($now - $currentSecond);
                if ($sleepTime > 0) {
                    usleep($sleepTime * 1000000);
                }
                self::$requestCount = 0;
                self::$lastRequestTime = floor(time());
            }
        } else {
            self::$requestCount = 0;
            self::$lastRequestTime = $currentSecond;
        }
        
        self::$requestCount++;
        self::$dailyRequestCount++;
        
        $headers = [
            'api_key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
        
        if (isset($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }
        
        $options['headers'] = $headers;
        $url = $this->apiBaseUrl . $endpoint;
        
        try {
            $response = $this->httpClient->request('GET', $url, $options);
            return $response->toArray();
        } catch (ClientExceptionInterface $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            
            if ($statusCode === Response::HTTP_TOO_MANY_REQUESTS) {
                if ($retry < self::MAX_RETRIES) {
                    $waitTime = (2 ** $retry) * 1000000; // Exponential backoff in microseconds
                    usleep($waitTime);
                    return $this->makeApiRequest($endpoint, $options, $retry + 1);
                }
                
                throw new \Exception('WMATA API request failed: Rate limit exceeded');
            } 
            
            throw new \Exception('WMATA API request failed: ' . $e->getMessage(), $statusCode);
        } catch (ServerExceptionInterface $e) {
            if ($retry < self::MAX_RETRIES) {
                $waitTime = (2 ** $retry) * 1000000; // Exponential backoff in microseconds
                usleep($waitTime);
                return $this->makeApiRequest($endpoint, $options, $retry + 1);
            }
            
            throw new \Exception('WMATA API server error after ' . self::MAX_RETRIES . ' retries');
        } catch (TransportExceptionInterface | RedirectionExceptionInterface $e) {
            if ($retry < self::MAX_RETRIES) {
                $waitTime = (2 ** $retry) * 1000000; // Exponential backoff in microseconds
                usleep($waitTime);
                return $this->makeApiRequest($endpoint, $options, $retry + 1);
            }
            
            throw new \Exception('WMATA API request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Manually clear the stations cache
     * 
     * @return bool
     */
    public function clearStationsCache(): bool
    {
        return $this->cache->delete('wmata_stations');
    }
}