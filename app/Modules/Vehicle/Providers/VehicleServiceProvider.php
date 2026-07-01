<?php

namespace App\Modules\Vehicle\Providers;

use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class VehicleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/vehicle.php');
        });

        // Public routes (no auth)
        Route::group($this->publicRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/public.php');
        });

        // Register Policy
        \Illuminate\Support\Facades\Gate::policy(Vehicle::class, \App\Modules\Vehicle\Policies\VehiclePolicy::class);

        // Listeners de eventos
        $this->app['events']->listen(
            \App\Modules\Vehicle\Events\VehicleRegistered::class,
            \App\Modules\Alerts\Listeners\CreateDefaultAlertRules::class
        );

        $this->app['events']->listen(
            \App\Modules\Vehicle\Events\KmUpdated::class,
            \App\Modules\Vehicle\Listeners\EvaluateMaintenanceAlerts::class
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
