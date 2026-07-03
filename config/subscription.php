<?php

return [
    'plans' => [
        'basic' => [
            'name' => 'Básico',
            'price' => 499, // €4.99 in cents
            'stripe_price_id' => env('STRIPE_PRICE_BASIC', 'price_basic_placeholder'),
            'features' => [
                'Hasta 3 vehículos',
                'Alertas básicas de caducidad',
                'Soporte por email',
            ],
            'vehicle_limit' => 3,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 999, // €9.99 in cents
            'stripe_price_id' => env('STRIPE_PRICE_PRO', 'price_pro_placeholder'),
            'features' => [
                'Vehículos ilimitados',
                'Alertas + OCR de facturas',
                'Informes PDF de venta',
                'Soporte prioritario',
            ],
            'vehicle_limit' => null, // unlimited
        ],
    ],
];
