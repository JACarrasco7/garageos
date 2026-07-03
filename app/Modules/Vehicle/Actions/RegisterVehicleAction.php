<?php

namespace App\Modules\Vehicle\Actions;

use App\Modules\Vehicle\Events\VehicleRegistered;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class RegisterVehicleAction
{
    /**
     * Register a new vehicle with its specifications.
     *
     * @throws \Exception
     */
    public function execute(array $data): Vehicle
    {
        return DB::transaction(function () use ($data) {
            $vehicle = Vehicle::create([
                'garage_id' => $data['garage_id'],
                'plate' => $data['plate'],
                'vin' => $data['vin'] ?? null,
                'brand' => $data['brand'],
                'model' => $data['model'],
                'year' => $data['year'],
                'registration_date' => $data['registration_date'] ?? null,
                'fuel_type' => $data['fuel_type'],
                'eco_label' => $data['eco_label'] ?? null,
                'emissions_co2' => $data['emissions_co2'] ?? null,
                'official_consumption' => $data['official_consumption'] ?? null,
                'color' => $data['color'] ?? null,
                'current_km' => $data['current_km'] ?? 0,
                'purchase_date' => $data['purchase_date'] ?? null,
                'purchase_price' => $data['purchase_price'] ?? null,
                'photo' => $data['photo'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (! empty($data['specs'])) {
                $vehicle->specs()->create($data['specs']);
            }

            event(new VehicleRegistered($vehicle));

            return $vehicle;
        });
    }
}
