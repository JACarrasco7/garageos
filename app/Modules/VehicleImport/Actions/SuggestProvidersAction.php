<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\Maintenance\Models\Workshop;

class SuggestProvidersAction
{
    public function execute(float $lat, float $lng, string $serviceType, int $radiusKm = 50): array
    {
        $workshops = Workshop::query()
            ->where('is_verified', true)
            ->where('services', 'like', '%'.$serviceType.'%')
            ->nearby($lat, $lng, $radiusKm)
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        return $workshops->map(function ($workshop) use ($lat, $lng) {
            return [
                'id' => $workshop->id,
                'name' => $workshop->name,
                'address' => $workshop->address,
                'city' => $workshop->city,
                'phone' => $workshop->phone,
                'email' => $workshop->email,
                'rating' => $workshop->rating,
                'distance' => $workshop->distanceFrom($lat, $lng),
                'services' => $workshop->services,
            ];
        })->toArray();
    }

    public function getImportServiceProviders(float $lat, float $lng): array
    {
        return $this->execute($lat, $lng, 'import', 100);
    }

    public function getTransportProviders(float $lat, float $lng): array
    {
        return $this->execute($lat, $lng, 'transport', 100);
    }

    public function getItvProviders(float $lat, float $lng): array
    {
        return $this->execute($lat, $lng, 'itv', 50);
    }
}