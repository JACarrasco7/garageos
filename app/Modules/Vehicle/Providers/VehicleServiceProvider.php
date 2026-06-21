<?php

namespace App\Modules\Vehicle\Providers;

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

        // Registrar listener para crear alertas por defecto
        $this->app['events']->listen(
            \App\Modules\Vehicle\Events\VehicleRegistered::class,
            \App\Modules\Alerts\Listeners\CreateDefaultAlertRules::class
        );
    }

    public function register(): void
    {
        //
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix' => 'api',
            'middleware' => ['web', 'auth'],
        ];
    }
}
