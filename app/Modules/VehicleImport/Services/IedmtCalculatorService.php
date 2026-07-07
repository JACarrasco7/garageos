<?php

namespace App\Modules\VehicleImport\Services;

class IedmtCalculatorService
{
    public function calculate(float $price, string $co2Emissions, int $age): float
    {
        $baseRate = 0.02;
        $co2Factor = (float) $co2Emissions * 0.001;
        $ageDiscount = max(0, (10 - $age) * 0.005);

        return $price * ($baseRate + $co2Factor - $ageDiscount);
    }
}
