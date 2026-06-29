<?php

namespace App\Http\Controllers\Api;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Maintenance\Models\ServicePack;
use Illuminate\Http\JsonResponse;

class VehicleController extends ApiController
{
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
