<?php

namespace Database\Seeders;

use App\Modules\Maintenance\Models\ServicePack;
use Illuminate\Database\Seeder;

class ServicePackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'maintenance_type' => 'aceite',
                'name' => 'Cambio de aceite básico',
                'items' => [
                    ['name' => 'Aceite de motor 5W30', 'reference' => '5L', 'price' => 45.00],
                    ['name' => 'Filtro de aceite', 'reference' => 'FO-123', 'price' => 12.00],
                ],
            ],
            [
                'maintenance_type' => 'filtros',
                'name' => 'Kit filtros completos',
                'items' => [
                    ['name' => 'Filtro de aire', 'reference' => 'FA-456', 'price' => 18.00],
                    ['name' => 'Filtro de habitáculo', 'reference' => 'FH-789', 'price' => 22.00],
                ],
            ],
        ];

        foreach ($packs as $pack) {
            ServicePack::create($pack);
        }
    }
}
