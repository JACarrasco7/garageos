<?php

namespace App\Modules\Maintenance\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MaintenanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/maintenance.php');
        });

        // Listeners
        $this->app['events']->listen(
            \App\Modules\Maintenance\Events\RevisionCompleted::class,
            \App\Modules\Maintenance\Listeners\UpdateVehicleScore::class
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
}
