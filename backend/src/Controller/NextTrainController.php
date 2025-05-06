<?php

namespace App\Controller;

use App\DTO\TrainInfoDTO;
use App\DTO\StationInfoDTO;
use App\Service\WmataApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class NextTrainController
{
    #[Route('/nextTrains/{station}', name: 'get_next_trains', methods: ['GET'])]
    public function getTrains(string $station, WmataApiService $apiClient): JsonResponse
    {
        try {
            $data = $apiClient->getNextTrains($station);
            // Transform the data using DTO
            $trainInfo = TrainInfoDTO::fromApiResponse($data['Trains']);
            
            return new JsonResponse([
                'status' => 'success',
                'data' => $trainInfo->toArray(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
    
    #[Route('/stations', name: 'get_stations', methods: ['GET'])]
    public function getStations(WmataApiService $apiClient): JsonResponse
    {
        try {
            $data = $apiClient->getStations();
            // Transform the data using DTO
            $stationInfo = StationInfoDTO::fromApiResponse($data);
            
            return new JsonResponse([
                'status' => 'success',
                'data' => $stationInfo->toArray(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
}