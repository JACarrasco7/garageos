<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Services\CarDataService;
use Illuminate\Http\JsonResponse;

class VehicleApiController extends Controller
{
    public function __construct(
        private CarDataService $carDataService
    ) {}

    public function getBrands(): JsonResponse
    {
        return response()->json($this->carDataService->getBrands());
    }

    public function getModels(string $brand, int $year): JsonResponse
    {
        return response()->json($this->carDataService->getModels($brand, $year));
    }

    public function decodeVin(string $vin): JsonResponse
    {
        $data = $this->carDataService->decodeVin($vin);

        if (! $data) {
            return response()->json(['error' => 'No se pudo decodificar el VIN'], 400);
        }

        return response()->json($data);
    }

    public function getSpanishSpecs(string $brand, string $model, int $year): JsonResponse
    {
        $specs = $this->carDataService->getSpanishSpecs($brand, $model, $year);

        return response()->json($specs);
    }
}
