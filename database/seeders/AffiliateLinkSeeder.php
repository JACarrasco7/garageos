<?php

namespace Database\Seeders;

use App\Modules\Marketplace\Models\AffiliateLink;
use Illuminate\Database\Seeder;

class AffiliateLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'provider' => 'autodoc',
                'product_name' => 'Filtro de Aceite Universal',
                'product_sku' => 'filter-oil',
                'affiliate_url' => 'https://www.autodoc.es/redirect?partner=garageos&sku=filter-oil',
                'price' => 8.99,
            ],
            [
                'provider' => 'autodoc',
                'product_name' => 'Aceite Motor 5W-30 5L',
                'product_sku' => 'oil-5w30',
                'affiliate_url' => 'https://www.autodoc.es/redirect?partner=garageos&sku=oil-5w30',
                'price' => 24.99,
            ],
            [
                'provider' => 'amazon',
                'product_name' => 'Pastillas de Freno Delanteras',
                'product_sku' => 'brake-pads-front',
                'affiliate_url' => 'https://www.amazon.es/dp/B0EXAMPLE?tag=garageos-21',
                'price' => 32.50,
            ],
            [
                'provider' => 'recambiosviaweb',
                'product_name' => 'Kit Distribución Completo',
                'product_sku' => 'timing-belt-kit',
                'affiliate_url' => 'https://www.recambiosviaweb.com/aff/garageos/timing-belt-kit',
                'price' => 89.00,
            ],
        ];

        foreach ($links as $link) {
            AffiliateLink::create(array_merge($link, [
                'currency' => 'EUR',
                'is_active' => true,
            ]));
        }
    }
}
