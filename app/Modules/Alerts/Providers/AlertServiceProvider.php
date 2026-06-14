<?php

namespace App\Modules\Alerts\Providers;

use Illuminate\Support\ServiceProvider;

class AlertServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Registrar listeners de alertas
        $this->app['events']->listen(
            \App\Modules\Alerts\Events\AlertTriggered::class,
            \App\Modules\Alerts\Listeners\SendNotification::class
        );
    }

    public function register(): void
    {
        //
    }
}