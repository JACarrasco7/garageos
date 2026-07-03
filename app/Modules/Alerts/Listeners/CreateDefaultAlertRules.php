<?php

namespace App\Modules\Alerts\Listeners;

use App\Modules\Alerts\Models\AlertRule;
use App\Modules\Vehicle\Events\VehicleRegistered;

class CreateDefaultAlertRules
{
    public function handle(VehicleRegistered $event): void
    {
        $vehicle = $event->vehicle;

        // Alertas por defecto para ITV (cada 1 año)
        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'itv',
            'advance_days' => 30,
        ]);

        // Alertas por defecto para Seguro (cada 1 año)
        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'seguro',
            'advance_days' => 30,
        ]);

        // Alertas por defecto para Aceite (cada 10,000 km o 6 meses)
        AlertRule::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'aceite',
            'advance_km' => 1000,
            'advance_days' => 180,
        ]);
    }
}
