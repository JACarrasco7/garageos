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
                'affiliate_link_ids' => [1, 2], // filter-oil, oil-5w30
            ],
            [
                'maintenance_type' => 'filtros',
                'name' => 'Kit filtros completos',
                'items' => [
                    ['name' => 'Filtro de aire', 'reference' => 'FA-456', 'price' => 18.00],
                    ['name' => 'Filtro de habitáculo', 'reference' => 'FH-789', 'price' => 22.00],
                ],
                'affiliate_link_ids' => [1], // filter-oil
            ],
            [
                'maintenance_type' => 'frenos',
                'name' => 'Cambio de frenos delanteros',
                'items' => [
                    ['name' => 'Pastillas de freno delanteras', 'reference' => 'PFD-001', 'price' => 65.00],
                    ['name' => 'Discos de freno delanteros', 'reference' => 'DFD-001', 'price' => 120.00],
                ],
                'affiliate_link_ids' => [3], // brake-pads-front
            ],
            [
                'maintenance_type' => 'distribucion',
                'name' => 'Kit de distribución completo',
                'items' => [
                    ['name' => 'Correa de distribución', 'reference' => 'CD-001', 'price' => 85.00],
                    ['name' => 'Tensor', 'reference' => 'T-001', 'price' => 45.00],
                    ['name' => 'Bomba de agua', 'reference' => 'BA-001', 'price' => 55.00],
                ],
                'affiliate_link_ids' => [4], // timing-belt-kit
            ],
        ];

        foreach ($packs as $pack) {
            ServicePack::updateOrCreate(
                ['maintenance_type' => $pack['maintenance_type']],
                $pack
            );
        }
    }
}
