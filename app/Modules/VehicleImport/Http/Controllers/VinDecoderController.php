<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\VehicleImport\Services\VinDecoderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VinDecoderController extends Controller
{
    public function __construct(protected VinDecoderService $vinDecoder) {}

    /**
     * Decode VIN and return vehicle specifications.
     */
    public function decode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vin' => ['required', 'string', 'size:17'],
        ]);

        try {
            $data = $this->vinDecoder->decode($validated['vin']);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo decodificar el VIN. Por favor, introduzca los datos manualmente.',
            ], 400);
        }
    }
}
