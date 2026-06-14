<?php

use App\Providers\AppServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Vehicle\Providers\VehicleServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    VehicleServiceProvider::class,
];
