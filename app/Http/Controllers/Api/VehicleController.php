<?php

namespace App\Http\Controllers\Api;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Maintenance\Models\ServicePack;
use Illuminate\Http\JsonResponse;

/**
 * @group Vehicle Endpoints
 *
 * APIs para acceso público a datos de vehículos vía QR.
 */
class VehicleController extends ApiController
{
    /**
     * Obtener datos del vehículo por QR token.
     *
     * Retorna información básica, especificaciones, documentos y mantenimientos.
     *
     * @urlParam qr_token string required El token QR único del vehículo. Example: abc123def456
     *
     * @response {
     *   "plate": "1234ABC",
     *   "brand": "Toyota",
     *   "model": "Corolla",
     *   "year": 2020,
     *   "current_km": 45000,
     *   "specs": { "engine_cc": 1600, "power_hp": 120 },
     *   "documents": [{ "type": "itv", "title": "ITV 2024" }],
     *   "maintenance": [{ "type": "aceite", "title": "Cambio de aceite" }]
     * }
     */
    public function show(string $qr_token): JsonResponse
    {
        $vehicle = Vehicle::where('qr_token', $qr_token)
            ->with(['specs', 'documents', 'maintenanceEntries'])
            ->firstOrFail();

        return response()->json([
            'plate' => $vehicle->plate,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'year' => $vehicle->year,
            'current_km' => $vehicle->current_km,
            'specs' => $vehicle->specs,
            'documents' => $vehicle->documents->map(fn($d) => [
                'type' => $d->type,
                'title' => $d->title,
                'document_date' => $d->document_date,
                'expiry_date' => $d->expiry_date,
            ]),
            'maintenance' => $vehicle->maintenanceEntries->map(fn($m) => [
                'type' => $m->type,
                'title' => $m->title,
                'service_date' => $m->service_date->format('Y-m-d'),
                'km_at_service' => $m->km_at_service,
                'cost' => $m->cost,
            ]),
        ]);
    }

    /**
     * Obtener service pack recomendado.
     *
     * Retorna el pack de servicios y enlaces de afiliado.
     *
     * @urlParam qr_token string required El token QR único del vehículo.
     * @urlParam type string required Tipo de servicio (aceite, filtros, etc). Example: aceite
     *
     * @response {
     *   "vehicle": "Toyota Corolla",
     *   "pack": { "name": "Aceite básico", "items": [...] },
     *   "affiliate_links": [{ "name": "Aceite 5W30", "affiliate_url": "https://..." }]
     * }
     *
     * @response 404 {
     *   "error": "Service pack not found"
     * }
     */
    public function servicePack(string $qr_token, string $type): JsonResponse
    {
        $vehicle = Vehicle::where('qr_token', $qr_token)->firstOrFail();

        $pack = ServicePack::where('maintenance_type', $type)->first();

        if (!$pack) {
            return response()->json(['error' => 'Service pack not found'], 404);
        }

        return response()->json([
            'vehicle' => $vehicle->brand . ' ' . $vehicle->model,
            'pack' => $pack,
            'affiliate_links' => $this->generateAffiliateLinks($pack->items),
        ]);
    }

    private function generateAffiliateLinks(array $items): array
    {
        return array_map(fn($item) => [
            'name' => $item['name'],
            'reference' => $item['reference'],
            'affiliate_url' => "https://autodoc.es/search?q={$item['reference']}&ref=garageos",
        ], $items);
    }
}
