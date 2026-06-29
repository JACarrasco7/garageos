<?php

namespace App\Modules\Maintenance\Actions;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Maintenance\Models\ServicePack;

class RecommendServicePackAction
{
    public function execute(Vehicle $vehicle): ?ServicePack
    {
        $recommendations = [
            'aceite' => $vehicle->current_km - ($vehicle->lastOilChangeKm() ?? 0) > 10000,
            'filtros' => $vehicle->current_km - ($vehicle->lastFilterChangeKm() ?? 0) > 20000,
            'neumaticos' => $vehicle->current_km - ($vehicle->lastTireChangeKm() ?? 0) > 40000,
            'frenos' => $vehicle->current_km - ($vehicle->lastBrakeChangeKm() ?? 0) > 50000,
        ];

        foreach ($recommendations as $type => $needsService) {
            if ($needsService) {
                return ServicePack::where('maintenance_type', $type)->first();
            }
        }

        return null;
    }
}
