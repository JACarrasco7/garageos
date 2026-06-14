<?php

use App\Providers\AppServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Vehicle\Providers\VehicleServiceProvider;
use App\Modules\Documents\Providers\DocumentServiceProvider;

return [
    AppServiceProvider::class,
    IdentityServiceProvider::class,
    VehicleServiceProvider::class,
    DocumentServiceProvider::class,
];
