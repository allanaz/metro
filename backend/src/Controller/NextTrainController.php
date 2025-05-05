<?php

namespace App\Controller;

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
            return new JsonResponse([
                'status' => 'success',
                'data' => $data["Trains"],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }

    #[Route('/stations', name: 'get_stations', methods: ['GET'])]
    public function getStations(WmataApiService $apiClient): JsonResponse
    {
        try {
            $data = $apiClient->getStations();
            return new JsonResponse([
                'status' => 'success',
                'data' => $data["Stations"],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}

