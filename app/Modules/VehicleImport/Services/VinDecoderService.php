<?php

namespace App\Modules\VehicleImport\Services;

use Illuminate\Support\Facades\Http;

class VinDecoderService
{
    /**
     * Decode VIN using NHTSA vPIC API.
     */
    public function decode(string $vin): array
    {
        $response = Http::get("https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/{$vin}", [
            'format' => 'json',
        ]);

        if (! $response->successful()) {
            throw new \Exception('API request failed');
        }

        $data = $response->json('Results.0');

        if (! $data) {
            throw new \Exception('No data found for VIN');
        }

        return [
            'make' => $data['Make'] ?? null,
            'model' => $data['Model'] ?? null,
            'year' => $this->extractYear($data['ModelYear'] ?? null),
            'engine_cc' => $this->extractEngineCc($data['DisplacementL'] ?? null),
            'power_kw' => $this->extractPowerKw($data['EngineHP'] ?? null),
            'co2_emissions' => $this->estimateCo2($data['FuelTypePrimary'] ?? null, $data['DisplacementL'] ?? null),
        ];
    }

    protected function extractYear(?string $modelYear): ?int
    {
        return $modelYear ? (int) $modelYear : null;
    }

    protected function extractEngineCc(?string $displacementL): ?int
    {
        if (! $displacementL) {
            return null;
        }

        return (int) round((float) $displacementL * 1000);
    }

    protected function extractPowerKw(?string $horsepower): ?int
    {
        if (! $horsepower) {
            return null;
        }

        return (int) round((float) $horsepower * 0.7457);
    }

    protected function estimateCo2(?string $fuelType, ?string $displacement): ?int
    {
        // Estimación simple basada en tipo de combustible y cilindrada
        if (! $displacement) {
            return null;
        }

        $liters = (float) $displacement;

        if (str_contains(strtolower($fuelType ?? ''), 'electric')) {
            return 0;
        }

        if (str_contains(strtolower($fuelType ?? ''), 'hybrid')) {
            return (int) round($liters * 80);
        }

        return (int) round($liters * 150);
    }

    /**
     * Validate VIN checksum (ISO 3779).
     */
    public function isValid(string $vin): bool
    {
        if (strlen($vin) !== 17) {
            return false;
        }

        // Validación básica: caracteres alfanuméricos
        if (! preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
            return false;
        }

        // Checksum para posición 9 (dígito de verificación)
        $weights = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];
        $chars = str_split($vin);
        $sum = 0;

        for ($i = 0; $i < 17; $i++) {
            if ($i === 8) {
                continue;
            } // Saltamos el dígito de verificación
            $char = $chars[$i];
            $value = is_numeric($char) ? (int) $char : (ord($char) - ord('A') + 1);
            $sum += $value * $weights[$i];
        }

        $checkDigit = $sum % 11;
        $expected = $checkDigit === 10 ? 'X' : (string) $checkDigit;

        return $chars[8] === $expected;
    }
}
