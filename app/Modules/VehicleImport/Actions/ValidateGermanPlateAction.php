<?php

namespace App\Modules\VehicleImport\Actions;

class ValidateGermanPlateAction
{
    public function execute(string $plate): bool
    {
        $normalized = strtoupper(str_replace('Ü', 'U', $plate));
        $pattern = '/^[A-Z]{1,3}-[A-Z]{1,2}-\d{1,4}$|^[A-Z]{1,3}\s[A-Z]{1,2}\s\d{1,4}$/';

        return preg_match($pattern, $normalized) === 1;
    }
}
