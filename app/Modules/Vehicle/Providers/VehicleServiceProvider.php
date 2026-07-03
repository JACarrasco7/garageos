<?php

namespace App\Modules\Vehicle\Providers;

use App\Modules\Alerts\Listeners\CreateDefaultAlertRules;
use App\Modules\Vehicle\Events\KmUpdated;
use App\Modules\Vehicle\Events\VehicleRegistered;
use App\Modules\Vehicle\Listeners\EvaluateMaintenanceAlerts;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\Vehicle\Models\VehiclePhoto;
use App\Modules\Vehicle\Observers\VehiclePhotoObserver;
use App\Modules\Vehicle\Policies\VehiclePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VehicleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Migrations are in the root database/migrations directory
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/vehicle.php');
        });

        // Public routes (no auth)
        Route::group($this->publicRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/public.php');
        });

        // Register Policy
        Gate::policy(Vehicle::class, VehiclePolicy::class);

        // Register Observer
        VehiclePhoto::observe(VehiclePhotoObserver::class);

        // Listeners de eventos
        $this->app['events']->listen(
            VehicleRegistered::class,
            CreateDefaultAlertRules::class
        );

        $this->app['events']->listen(
            KmUpdated::class,
            EvaluateMaintenanceAlerts::class
        );
    }

    public function register(): void
    {
        //
    }

    protected function routeConfiguration(): array
    {
        return [
            'middleware' => ['web', 'auth'],
        ];
    }

    protected function publicRouteConfiguration(): array
    {
        return [
            'middleware' => ['web'],
        ];
    }
}
