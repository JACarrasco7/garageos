<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Permissions Matrix
    |--------------------------------------------------------------------------
    | Define qué planes tienen acceso a cada funcionalidad
    */

    'features' => [
        'vehicles.create' => [
            'description' => 'Crear vehículos',
            'plans' => ['free', 'pro', 'importer'],
            'limits' => [
                'free' => 3,
                'pro' => null,
                'importer' => null,
            ],
        ],
        'marketplace.view' => [
            'description' => 'Ver anuncios del mercado',
            'plans' => ['free', 'pro', 'importer'],
        ],
        'marketplace.create' => [
            'description' => 'Crear anuncios',
            'plans' => ['pro', 'importer'],
        ],
        'marketplace.buy' => [
            'description' => 'Comprar vehículos',
            'plans' => ['pro', 'importer'],
        ],
        'imports.create' => [
            'description' => 'Crear solicitudes de importación',
            'plans' => ['pro', 'importer'],
        ],
        'imports.receive' => [
            'description' => 'Recibir ofertas de importación',
            'plans' => ['importer'],
        ],
        'documents.ocr' => [
            'description' => 'OCR de facturas',
            'plans' => ['pro', 'importer'],
        ],
        'reports.pdf' => [
            'description' => 'Generar informes PDF',
            'plans' => ['pro', 'importer'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Plan Definitions
    |--------------------------------------------------------------------------
    */

    'plans' => [
        'free' => [
            'name' => 'Gratuito',
            'price' => 0,
            'interval' => 'month',
            'stripe_price_id' => null,
            'features' => ['vehicles.create', 'marketplace.view'],
            'description' => 'Perfecto para empezar',
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 999,
            'interval' => 'month',
            'stripe_price_id' => env('STRIPE_PRICE_PRO', 'price_pro_placeholder'),
            'features' => [
                'vehicles.create',
                'marketplace.view',
                'marketplace.create',
                'marketplace.buy',
                'imports.create',
                'documents.ocr',
                'reports.pdf',
            ],
            'description' => 'Para usuarios avanzados',
        ],
        'importer' => [
            'name' => 'Importador',
            'price' => 1999,
            'interval' => 'month',
            'stripe_price_id' => env('STRIPE_PRICE_IMPORTER', 'price_importer_placeholder'),
            'features' => [
                'vehicles.create',
                'marketplace.view',
                'marketplace.create',
                'marketplace.buy',
                'imports.create',
                'imports.receive',
                'documents.ocr',
                'reports.pdf',
            ],
            'description' => 'Para profesionales importadores',
        ],
    ],
];
