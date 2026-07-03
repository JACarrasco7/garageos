<?php

namespace App\Modules\Maintenance\Providers;

use App\Modules\Maintenance\Events\RevisionCompleted;
use App\Modules\Maintenance\Listeners\UpdateVehicleScore;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MaintenanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Migrations are in the root database/migrations directory
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/maintenance.php');
        });

        // Public routes (no auth)
        Route::group($this->publicRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/public_workshops.php');
        });

        // Listeners
        $this->app['events']->listen(
            RevisionCompleted::class,
            UpdateVehicleScore::class
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
