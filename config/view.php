<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => storage_path('framework/views'),

    'cache' => env('VIEW_CACHE', false),

    'check_cache_timestamps' => env('VIEW_CHECK_CACHE_TIMESTAMPS', true),

    'compiled_extension' => env('VIEW_COMPILED_EXTENSION', 'php'),

    'relative_hash' => env('VIEW_RELATIVE_HASH', false),
];
