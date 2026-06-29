<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Registrar módulos
        $this->app->register(\App\Modules\Vehicle\Providers\VehicleServiceProvider::class);
        $this->app->register(\App\Modules\Identity\Providers\IdentityServiceProvider::class);
        $this->app->register(\App\Modules\Documents\Providers\DocumentServiceProvider::class);
        $this->app->register(\App\Modules\Maintenance\Providers\MaintenanceServiceProvider::class);
        $this->app->register(\App\Modules\Alerts\Providers\AlertServiceProvider::class);
        $this->app->register(\App\Modules\Marketplace\Providers\MarketplaceServiceProvider::class);
        $this->app->register(\App\Providers\FirebaseServiceProvider::class);
    }
}
