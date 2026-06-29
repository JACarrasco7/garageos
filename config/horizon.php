<?php

return [

    'use' => 'default',

    'path' => 'horizon',

    'route_middleware' => ['web', 'auth'],

    'supervisor' => [
        'default' => [
            'connection' => 'redis',
            'queue' => ['default', 'alerts', 'documents', 'reports'],
            'balance' => 'auto',
            'processes' => 3,
            'tries' => 3,
            'timeout' => 90,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor.default.processes' => 6,
            'supervisor.default.timeout' => 120,
        ],
    ],

];
