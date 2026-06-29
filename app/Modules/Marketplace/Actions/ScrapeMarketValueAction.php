<?php

namespace App\Modules\Marketplace\Actions;

use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Marketplace\Models\MarketValue;
use Illuminate\Support\Facades\Http;

class ScrapeMarketValueAction
{
    public function execute(Vehicle $vehicle): ?MarketValue
    {
        $searchTerm = "{$vehicle->brand} {$vehicle->model} {$vehicle->year}";

        // Placeholder: integrar con API real (coches.net, autocasion)
        // $response = Http::get('https://api.example.com/vehicles/search', [
        //     'q' => $searchTerm,
        //     'year' => $vehicle->year,
        // ]);

        // Simular valor estimado
        $estimatedValue = $this->estimateValue($vehicle);

        if (!$estimatedValue) {
            return null;
        }

        return MarketValue::create([
            'vehicle_id' => $vehicle->id,
            'estimated_value' => $estimatedValue,
            'min_value' => $estimatedValue * 0.85,
            'max_value' => $estimatedValue * 1.15,
            'source' => 'estimated',
        ]);
    }

    private function estimateValue(Vehicle $vehicle): float
    {
        $basePrice = match($vehicle->brand) {
            'Seat', 'Volkswagen', 'Renault', 'Peugeot', 'Citroën' => 15000,
            'BMW', 'Audi' => 25000,
            'Toyota', 'Honda' => 20000,
            default => 12000,
        };

        $ageFactor = max(0, (now()->year - $vehicle->year) * 0.15);
        $kmFactor = $vehicle->current_km / 100000 * 0.3;

        return max(1000, $basePrice * (1 - $ageFactor - $kmFactor));
    }
}