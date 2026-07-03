<?php

namespace App\Modules\Alerts\Providers;

use App\Modules\Alerts\Events\AlertTriggered;
use App\Modules\Alerts\Listeners\SendNotification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AlertServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Cargar rutas
        Route::group(['middleware' => ['web', 'auth', 'verified']], function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/alerts.php');
        });

        // Registrar listeners de alertas
        $this->app['events']->listen(
            AlertTriggered::class,
            SendNotification::class
        );
    }

    public function register(): void
    {
        //
    }
}
