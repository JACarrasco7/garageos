<?php

namespace App\Modules\Vehicle\Actions;

use App\Modules\Vehicle\Models\KmHistory;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class UpdateKmAction
{
    /**
     * Record a new kilometer entry and update the vehicle's current mileage.
     *
     * @throws \Exception
     */
    public function execute(Vehicle $vehicle, array $data): KmHistory
    {
        return DB::transaction(function () use ($vehicle, $data) {
            $kmEntry = $vehicle->kmHistory()->create([
                'km' => $data['km'],
                'recorded_at' => $data['recorded_at'],
                'source' => $data['source'] ?? 'manual',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update the vehicle's current mileage if the new entry is more recent or higher
            if ($data['km'] > $vehicle->current_km) {
                $vehicle->update([
                    'current_km' => $data['km'],
                ]);
            }

            return $kmEntry;
        });
    }
}
