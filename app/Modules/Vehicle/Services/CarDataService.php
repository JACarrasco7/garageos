<?php

namespace App\Modules\Vehicle\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CarDataService
{
    private const NHTSA_BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    private const CACHE_TTL = 86400; // 24 horas

    public function decodeVin(string $vin): ?array
    {
        if (strlen($vin) !== 17) {
            return null;
        }

        $cacheKey = "vin_decode_{$vin}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($vin) {
            $response = Http::timeout(5)->get(self::NHTSA_BASE_URL."/DecodeVin/{$vin}?format=json");

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return $this->parseNhtsaResponse($data);
        });
    }

    public function getBrands(): array
    {
        $cacheKey = 'car_brands';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $response = Http::timeout(5)->get(self::NHTSA_BASE_URL.'/GetMakesForVehicleType/car?format=json');

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $brands = collect($data['Results'] ?? [])
                ->map(fn ($item) => [
                    'name' => $item['Make_Name'],
                    'id' => $item['Make_ID'],
                ])
                ->unique('name')
                ->sortBy('name')
                ->values()
                ->all();

            return $brands;
        });
    }

    public function getModels(string $brand, int $year): array
    {
        $cacheKey = "car_models_{$brand}_{$year}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($brand, $year) {
            $response = Http::timeout(5)->get(
                self::NHTSA_BASE_URL."/GetModelsForMakeYear/make/{$brand}/modelyear/{$year}?format=json"
            );

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $models = collect($data['Results'] ?? [])
                ->map(fn ($item) => [
                    'name' => $item['Model_Name'],
                    'id' => $item['Model_ID'],
                ])
                ->unique('name')
                ->sortBy('name')
                ->values()
                ->all();

            return $models;
        });
    }

    private function parseNhtsaResponse(array $data): array
    {
        $results = $data['Results'] ?? [];
        $parsed = [];

        foreach ($results as $item) {
            $variable = $item['Variable'] ?? '';
            $value = $item['Value'] ?? '';

            match ($variable) {
                'Make' => $parsed['brand'] = $value,
                'Model' => $parsed['model'] = $value,
                'Model Year' => $parsed['year'] = (int) $value,
                'Engine Model' => $parsed['engine'] = $value,
                'Engine Cylinders' => $parsed['cylinders'] = (int) $value,
                'Displacement (CCM)' => $parsed['engine_cc'] = (int) str_replace(' CC', '', $value),
                'Engine Power (kW)' => $parsed['power_kw'] = (int) $value,
                'Transmission Style' => $parsed['transmission'] = $this->normalizeTransmission($value),
                'Drive Type' => $parsed['drive'] = $this->normalizeDrive($value),
                'Number of Doors' => $parsed['doors'] = (int) $value,
                'Number of Seats' => $parsed['seats'] = (int) $value,
                'Body Class' => $parsed['body_type'] = $value,
                default => null,
            };
        }

        if (isset($parsed['power_kw'])) {
            $parsed['power_hp'] = (int) round($parsed['power_kw'] * 1.341);
        }

        return $parsed;
    }

    private function normalizeTransmission(string $value): string
    {
        return match (true) {
            str_contains(strtolower($value), 'automatic') => 'automatico',
            str_contains(strtolower($value), 'manual') => 'manual',
            str_contains(strtolower($value), 'cvt') => 'cvt',
            default => 'automatico',
        };
    }

    private function normalizeDrive(string $value): string
    {
        return match (true) {
            str_contains(strtolower($value), '4wd') || str_contains(strtolower($value), '4x4') => '4wd',
            str_contains(strtolower($value), 'awd') => 'awd',
            str_contains(strtolower($value), 'rwd') || str_contains(strtolower($value), 'rear') => 'rwd',
            str_contains(strtolower($value), 'fwd') || str_contains(strtolower($value), 'front') => 'fwd',
            default => 'fwd',
        };
    }

    public function getSpanishSpecs(string $brand, string $model, int $year): array
    {
        $cacheKey = "spanish_specs_{$brand}_{$model}_{$year}";

        return Cache::remember($cacheKey, self::CACHE_TTL * 7, function () use ($brand, $model, $year) {
            $specs = $this->fetchFromCo2API($brand, $model, $year);

            if (empty($specs)) {
                $specs = $this->estimateFromFuelType($brand, $model, $year);
            }

            return $specs;
        });
    }

    private function fetchFromCo2API(string $brand, string $model, int $year): array
    {
        try {
            $response = Http::timeout(5)->get(
                'https://data.gov.uk/resource/gbem-sqj7.json',
                [
                    'manufacturer' => $brand,
                    'model' => $model,
                    'year' => $year,
                ]
            );

            if (! $response->successful()) {
                return [];
            }

            $data = collect($response->json())->first();

            if (! $data) {
                return [];
            }

            return [
                'emissions_co2' => (float) ($data['emissions_co2'] ?? 0),
                'official_consumption' => (float) ($data['combined_metric_combined'] ?? 0),
                'eco_label' => $this->calculateEcoLabel((float) ($data['emissions_co2'] ?? 0), $data['fuel_type'] ?? ''),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function estimateFromFuelType(string $brand, string $model, int $year): array
    {
        return [
            'emissions_co2' => $this->estimateCo2ByYear($year),
            'official_consumption' => $this->estimateConsumptionByYear($year),
            'eco_label' => $this->estimateEcoLabelByYear($year),
        ];
    }

    private function estimateCo2ByYear(int $year): float
    {
        $base = 2024;
        $modernValue = 120.0;

        if ($year >= $base) {
            return $modernValue;
        }

        $diff = $base - $year;
        $increment = $diff * 2.5;

        return min($modernValue + $increment, 250.0);
    }

    private function estimateConsumptionByYear(int $year): float
    {
        $base = 2024;
        $modernValue = 5.5;

        if ($year >= $base) {
            return $modernValue;
        }

        $diff = $base - $year;
        $increment = $diff * 0.1;

        return min($modernValue + $increment, 12.0);
    }

    private function estimateEcoLabelByYear(int $year): string
    {
        $co2 = $this->estimateCo2ByYear($year);

        if ($co2 <= 50) {
            return 'Zero';
        }

        if ($co2 <= 100) {
            return 'ECO';
        }

        if ($co2 <= 150) {
            return 'C';
        }

        return 'B';
    }

    private function calculateEcoLabel(float $co2, string $fuelType): string
    {
        if ($co2 <= 0) {
            return null;
        }

        $fuelLower = strtolower($fuelType);

        $isElectric = str_contains($fuelLower, 'electric') || str_contains($fuelLower, 'ev') || str_contains($fuelLower, 'battery');
        $isHybrid = str_contains($fuelLower, 'hybrid') || str_contains($fuelLower, 'plug-in');
        $isGas = str_contains($fuelLower, 'gas') || str_contains($fuelLower, 'lpg') || str_contains($fuelLower, 'cng');

        if ($isElectric && $co2 <= 0) {
            return 'Zero';
        }

        if ($isElectric || ($isHybrid && $co2 <= 50)) {
            return 'ECO';
        }

        if ($co2 <= 100 || ($isHybrid && $co2 <= 120)) {
            return 'C';
        }

        if ($co2 <= 150) {
            return 'B';
        }

        return 'B';
    }
}
