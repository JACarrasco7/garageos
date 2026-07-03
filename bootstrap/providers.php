<?php

use App\Modules\Alerts\Providers\AlertServiceProvider;
use App\Modules\Documents\Providers\DocumentServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Listings\Providers\ListingsServiceProvider;
use App\Modules\Maintenance\Providers\MaintenanceServiceProvider;
use App\Modules\Marketplace\Providers\MarketplaceServiceProvider;
use App\Modules\Vehicle\Providers\VehicleServiceProvider;
use App\Modules\VehicleImport\Providers\VehicleImportServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FirebaseServiceProvider;

return [
    AppServiceProvider::class,
    FirebaseServiceProvider::class,
    IdentityServiceProvider::class,
    VehicleServiceProvider::class,
    DocumentServiceProvider::class,
    AlertServiceProvider::class,
    MaintenanceServiceProvider::class,
    MarketplaceServiceProvider::class,
    VehicleImportServiceProvider::class,
    ListingsServiceProvider::class,
];
