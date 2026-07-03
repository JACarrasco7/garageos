<?php

namespace App\Modules\Marketplace\Actions;

use App\Modules\Marketplace\Models\Valuation;

class ValuationAction
{
    protected array $depreciationRates = [
        'luxury' => [0.15, 0.12, 0.10, 0.08, 0.06, 0.05, 0.05, 0.05, 0.05, 0.05],
        'sports' => [0.20, 0.18, 0.15, 0.12, 0.10, 0.08, 0.07, 0.06, 0.05, 0.05],
        'standard' => [0.12, 0.10, 0.08, 0.06, 0.05, 0.04, 0.04, 0.03, 0.03, 0.02],
    ];

    public function execute(array $data): Valuation
    {
        $brand = $data['brand'] ?? '';
        $model = $data['model'] ?? '';
        $year = $data['year'] ?? now()->year;
        $mileage = $data['mileage_km'] ?? 0;
        $fuelType = $data['fuel_type'] ?? 'gasoline';
        $powerHp = $data['power_hp'] ?? 0;

        $basePrice = $this->getBasePrice($brand, $model, $year);
        $age = now()->year - $year;

        $category = $this->getVehicleCategory($brand, $model);
        $depreciation = $this->calculateDepreciation($age, $mileage, $category);

        $estimatedValue = $basePrice * (1 - $depreciation);
        $confidenceScore = $this->calculateConfidenceScore($data);

        return Valuation::create([
            'brand' => $brand,
            'model' => $model,
            'year' => $year,
            'mileage_km' => $mileage,
            'fuel_type' => $fuelType,
            'power_hp' => $powerHp,
            'estimated_value' => max(100, $estimatedValue),
            'min_value' => max(100, $estimatedValue * 0.85),
            'max_value' => $estimatedValue * 1.15,
            'confidence_score' => $confidenceScore,
            'data_source' => $data['source'] ?? 'manual',
            'last_updated' => now(),
        ]);
    }

    protected function getBasePrice(string $brand, string $model, int $year): float
    {
        $referencePrices = [
            'audi' => ['A3' => 30000, 'A4' => 35000, 'A6' => 45000],
            'bmw' => ['1 series' => 28000, '3 series' => 38000, '5 series' => 48000],
            'mercedes' => ['A-Class' => 35000, 'C-Class' => 42000, 'E-Class' => 52000],
            'vw' => ['Golf' => 25000, 'Passat' => 30000, 'Tiguan' => 32000],
            'ford' => ['Focus' => 20000, 'Puma' => 25000, 'Transit' => 28000],
        ];

        $brandKey = strtolower($brand);
        $modelKey = strtolower($model);

        if (isset($referencePrices[$brandKey][$modelKey])) {
            return $referencePrices[$brandKey][$modelKey];
        }

        return 25000;
    }

    protected function getVehicleCategory(string $brand, string $model): string
    {
        $luxury = ['audi', 'bmw', 'mercedes', 'audi', 'porsche', 'jaguar', 'land rover'];
        $sports = ['audi rs', 'bmw m', 'mercedes amg', 'porsche'];

        $brandKey = strtolower($brand);
        $modelKey = strtolower($model);

        if (in_array($brandKey, $luxury) || str_contains($modelKey, 's ')) {
            return 'luxury';
        }

        if (in_array($brandKey, $sports) || str_contains($modelKey, 'gt') || str_contains($modelKey, 'amg')) {
            return 'sports';
        }

        return 'standard';
    }

    protected function calculateDepreciation(int $age, int $mileage, string $category): float
    {
        $rates = $this->depreciationRates[$category] ?? $this->depreciationRates['standard'];
        $totalDepreciation = 0;

        for ($i = 0; $i < min($age, count($rates)); $i++) {
            $mileageFactor = min(0.5, $mileage / 200000);
            $totalDepreciation += $rates[$i] + ($mileageFactor * 0.1);
        }

        return min(0.8, $totalDepreciation);
    }

    protected function calculateConfidenceScore(array $data): float
    {
        $score = 0;
        $fields = ['brand', 'model', 'year', 'mileage_km', 'fuel_type', 'power_hp'];

        foreach ($fields as $field) {
            if (! empty($data[$field])) {
                $score += 15;
            }
        }

        return min(100, $score);
    }
}
