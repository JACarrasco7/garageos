<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\Marketplace\Models\Valuation;
use App\Modules\VehicleImport\Models\VehicleImport;

class ImportValuationAction
{
    public function execute(VehicleImport $import): array
    {
        $valuation = Valuation::calculate([
            'brand' => $import->brand,
            'model' => $import->model,
            'year' => $import->year,
            'mileage_km' => 0,
            'fuel_type' => 'gasoline',
            'power_hp' => $import->power_kw ? (int) ($import->power_kw * 1.341) : 100,
        ]);

        $taxes = $this->calculateImportTaxes($import, $valuation->estimated_value);

        return [
            'valuation' => $valuation,
            'taxes' => $taxes,
            'total_cost' => $valuation->estimated_value + $taxes['total_tax'],
        ];
    }

    public function calculateImportTaxes(VehicleImport $import, float $purchasePrice): array
    {
        $co2 = $import->co2_emissions ?? 120;
        $age = now()->year - $import->year;
        $country = $import->origin_country ?? 'DE';

        // Tarifas por país de origen
        $countryRates = [
            'DE' => ['iedmt' => 0.00, 'itp' => 0.00, 'import_duty' => 0.00, 'vat' => 0.19],
            'FR' => ['iedmt' => 0.00, 'itp' => 0.00, 'import_duty' => 0.00, 'vat' => 0.20],
            'IT' => ['iedmt' => 0.00, 'itp' => 0.00, 'import_duty' => 0.00, 'vat' => 0.22],
            'NL' => ['iedmt' => 0.00, 'itp' => 0.00, 'import_duty' => 0.00, 'vat' => 0.21],
            'ES' => ['iedmt' => 0.00, 'itp' => 0.00, 'import_duty' => 0.00, 'vat' => 0.21],
        ];

        $rates = $countryRates[$country] ?? $countryRates['DE'];

        // Si ya está matriculado en UE, no hay aranceles
        $importDuty = 0.00;
        $vat = $purchasePrice * $rates['vat'];

        // Cálculo IEDMT (España) basado en CO2
        $depreciationCoeff = match (true) {
            $age < 1 => 0.84,
            $age < 2 => 0.67,
            $age < 3 => 0.56,
            $age < 4 => 0.47,
            $age < 5 => 0.39,
            $age < 6 => 0.33,
            $age < 7 => 0.28,
            $age < 8 => 0.24,
            $age < 9 => 0.18,
            $age < 10 => 0.14,
            default => 0.10,
        };

        $catalogPrice = $purchasePrice / $depreciationCoeff;

        $iedmtTaxRate = match (true) {
            $co2 <= 120 => 0.00,
            $co2 <= 159 => 0.0475,
            $co2 <= 199 => 0.0975,
            default => 0.1475,
        };

        $iedmt = $catalogPrice * $iedmtTaxRate;

        // ITP (Impuesto sobre Tráfico de Vehículos)
        $itpRate = match (true) {
            $age < 6 => 0.10,
            $age < 8 => 0.08,
            default => 0.04,
        };
        $itp = $purchasePrice * $itpRate;

        return [
            'catalog_value' => round($catalogPrice, 2),
            'iedmt' => round($iedmt, 2),
            'itp_estimate' => round($itp, 2),
            'import_duty' => round($importDuty, 2),
            'vat' => round($vat, 2),
            'total_tax' => round($iedmt + $itp + $importDuty + $vat, 2),
            'co2_rate' => $iedmtTaxRate,
            'depreciation_coefficient' => $depreciationCoeff,
            'origin_country' => $country,
        ];
    }
}
