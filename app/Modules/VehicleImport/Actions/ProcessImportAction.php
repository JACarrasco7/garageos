<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Identity\Models\Garage;

class ProcessImportAction
{
    public function execute(VehicleImport $import): Vehicle
    {
        $import->update(['status' => 'processing']);

        $garage = Garage::firstOrCreate(['user_id' => $import->user_id]);

        $vehicle = Vehicle::create([
            'garage_id' => $garage->id,
            'plate' => $import->plate_new,
            'brand' => $import->brand,
            'model' => $import->model,
            'year' => $import->year,
        ]);

        if ($import->engine_cc || $import->power_kw) {
            $vehicle->specs()->create([
                'engine_cc' => $import->engine_cc,
                'power_kw' => $import->power_kw,
            ]);
        }

        $import->update(['status' => 'approved', 'vehicle_id' => $vehicle->id]);

        // Disparar evento para crear alertas por defecto
        event(new \App\Modules\Vehicle\Events\VehicleRegistered($vehicle));

        return $vehicle;
    }
}