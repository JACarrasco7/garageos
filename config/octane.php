<?php

return [

    'server' => env('OCTANE_SERVER', 'frankenphp'),

    'listeners' => [
        'redis' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', 6379),
        ],
    ],

    'warmup' => [
        'paths' => [
            '/',
            '/dashboard',
        ],
    ],

    'cache' => [
        'rows' => 1000,
        'bytes' => 10000,
    ],

    'tables' => [
        'cache',
        'jobs',
    ],

    'flush_commands' => [
        'config:clear',
        'route:clear',
        'view:clear',
    ],

];