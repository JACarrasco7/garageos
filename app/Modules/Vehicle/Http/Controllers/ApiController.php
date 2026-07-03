<?php

namespace App\Modules\Vehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Services\CarDataService;

class ApiController extends Controller
{
    public function __construct(
        private CarDataService $carDataService,
    ) {}

    public function brands()
    {
        $brands = $this->carDataService->getBrands();

        return response()->json($brands);
    }

    public function models(string $brand, int $year)
    {
        $models = $this->carDataService->getModels($brand, $year);

        return response()->json($models);
    }

    public function decodeVin(string $vin)
    {
        $data = $this->carDataService->decodeVin($vin);

        if (! $data) {
            return response()->json(['error' => 'No se pudo decodificar el VIN'], 404);
        }

        return response()->json($data);
    }
}
