<?php

use App\Providers\AppServiceProvider;
use App\Providers\FirebaseServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Vehicle\Providers\VehicleServiceProvider;
use App\Modules\Documents\Providers\DocumentServiceProvider;
use App\Modules\Alerts\Providers\AlertServiceProvider;
use App\Modules\Maintenance\Providers\MaintenanceServiceProvider;
use App\Modules\Marketplace\Providers\MarketplaceServiceProvider;
use App\Modules\VehicleImport\Providers\VehicleImportServiceProvider;

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
];
