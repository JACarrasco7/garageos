<?php

use App\Modules\Alerts\Providers\AlertServiceProvider;
use App\Modules\Billing\Providers\BillingServiceProvider;
use App\Modules\Documents\Providers\DocumentServiceProvider;
use App\Modules\Identity\Providers\IdentityServiceProvider;
use App\Modules\Listings\Providers\ListingsServiceProvider;
use App\Modules\Maintenance\Providers\MaintenanceServiceProvider;
use App\Modules\Marketplace\Providers\MarketplaceServiceProvider;
use App\Modules\Messaging\Providers\MessagingServiceProvider;
use App\Modules\Providers\Providers\ProvidersServiceProvider;
use App\Modules\Vehicle\Providers\VehicleServiceProvider;
use App\Modules\VehicleImport\Providers\VehicleImportServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FirebaseServiceProvider;
use App\Providers\ImportPaymentServiceProvider;

return [
    AlertServiceProvider::class,
    BillingServiceProvider::class,
    DocumentServiceProvider::class,
    IdentityServiceProvider::class,
    ListingsServiceProvider::class,
    MaintenanceServiceProvider::class,
    MarketplaceServiceProvider::class,
    MessagingServiceProvider::class,
    ProvidersServiceProvider::class,
    VehicleImportServiceProvider::class,
    VehicleServiceProvider::class,
    AppServiceProvider::class,
    FirebaseServiceProvider::class,
    ImportPaymentServiceProvider::class,
];
