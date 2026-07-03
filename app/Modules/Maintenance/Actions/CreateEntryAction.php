<?php

namespace App\Modules\Maintenance\Actions;

use App\Modules\Maintenance\Models\MaintenanceEntry;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class CreateEntryAction
{
    /**
     * Create a new maintenance entry.
     *
     * @throws \Exception
     */
    public function execute(Vehicle $vehicle, array $data): MaintenanceEntry
    {
        return DB::transaction(function () use ($vehicle, $data) {
            $entry = MaintenanceEntry::create([
                'vehicle_id' => $vehicle->id,
                'document_id' => $data['document_id'] ?? null,
                'workshop_id' => $data['workshop_id'] ?? null,
                'type' => $data['type'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'km_at_service' => $data['km_at_service'],
                'service_date' => $data['service_date'],
                'cost' => $data['cost'] ?? null,
                'is_verified' => $data['is_verified'] ?? false,
                'notes' => $data['notes'] ?? null,
            ]);

            // If the entry includes a kilometer reading, update the vehicle's current mileage if it's higher
            if ($data['km_at_service'] > $vehicle->current_km) {
                $vehicle->update([
                    'current_km' => $data['km_at_service'],
                ]);
            }

            return $entry;
        });
    }
}
