<?php

return [
    'plans' => [
        'basic' => [
            'name' => 'Básico',
            'price' => 499, // €4.99 in cents
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
