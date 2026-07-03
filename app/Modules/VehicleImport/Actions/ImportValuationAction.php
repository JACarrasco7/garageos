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
            'total_cost' => $valuation->estimated_value + $taxes['iedmt'] + $taxes['itp_estimate'],
        ];
    }

    public function calculateImportTaxes(VehicleImport $import, float $purchasePrice): array
    {
        $co2 = $import->co2_emissions ?? 120;
        $age = now()->year - $import->year;

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

        $taxRate = match (true) {
            $co2 <= 120 => 0.00,
            $co2 <= 159 => 0.0475,
            $co2 <= 199 => 0.0975,
            default => 0.1475,
        };

        $catalogPrice = $purchasePrice / $depreciationCoeff;
        $iedmt = $catalogPrice * $taxRate;
        $itpRate = 0.10;
        $itp = $purchasePrice * $itpRate;

        return [
            'catalog_value' => round($catalogPrice, 2),
            'iedmt' => round($iedmt, 2),
            'itp_estimate' => round($itp, 2),
            'co2_rate' => $taxRate,
            'depreciation_coefficient' => $depreciationCoeff,
        ];
    }
}
