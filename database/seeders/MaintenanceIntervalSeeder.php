<?php

namespace Database\Seeders;

use App\Modules\Maintenance\Models\MaintenanceInterval;
use Illuminate\Database\Seeder;

class MaintenanceIntervalSeeder extends Seeder
{
    public function run(): void
    {
        $intervals = [
            // Aceite - genérico
            ['type' => 'aceite', 'interval_km' => 10000, 'interval_months' => 6, 'description' => 'Cambio de aceite'],
            // Neumáticos
            ['type' => 'neumaticos', 'interval_km' => 40000, 'interval_months' => 36, 'description' => 'Revisión neumáticos'],
            // Frenos
            ['type' => 'frenos', 'interval_km' => 30000, 'interval_months' => 24, 'description' => 'Revisión frenos'],
            // ITV
            ['type' => 'itv', 'interval_months' => 12, 'description' => 'Inspección Técnica'],
            // Seguro
            ['type' => 'seguro', 'interval_months' => 12, 'description' => 'Renovación seguro'],
        ];

        foreach ($intervals as $interval) {
            MaintenanceInterval::create($interval);
        }
    }
}
