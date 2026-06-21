<?php

namespace App\Modules\Marketplace\Actions;

use App\Modules\Vehicle\Models\Vehicle;

class CalculateScoreAction
{
    public function execute(Vehicle $vehicle): int
    {
        $score = 50; // Base

        // +20 si tiene documentos verificados
        if ($vehicle->documents()->where('is_verified', true)->exists()) {
            $score += 20;
        }

        // +15 si tiene historial de mantenimiento completo
        $maintenanceCount = $vehicle->maintenanceEntries()->count();
        if ($maintenanceCount >= 5) {
            $score += 15;
        } elseif ($maintenanceCount >= 2) {
            $score += 8;
        }

        // +10 si tiene menos de 100,000 km
        if ($vehicle->current_km < 100000) {
            $score += 10;
        }

        // +5 si tiene especificaciones completas
        if ($vehicle->specs && $vehicle->specs->engine_cc) {
            $score += 5;
        }

        // -10 si tiene alertas activas sin resolver
        $pendingAlerts = $vehicle->alertRules()
            ->where('is_active', true)
            ->whereNull('last_triggered')
            ->count();
        if ($pendingAlerts > 0) {
            $score -= 10;
        }

        return max(0, min(100, $score));
    }
}
