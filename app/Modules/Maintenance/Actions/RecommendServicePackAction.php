<?php

namespace App\Modules\Maintenance\Actions;

use App\Modules\Maintenance\Models\ServicePack;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Support\Collection;

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

    /**
     * Get affiliate links for the recommended service pack for a vehicle.
     */
    public function getAffiliateLinksForVehicle(Vehicle $vehicle): Collection
    {
        $pack = $this->execute($vehicle);

        if (! $pack) {
            return collect();
        }

        return $pack->getAffiliateLinks();
    }
}
