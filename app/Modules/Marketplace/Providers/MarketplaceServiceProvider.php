<?php

namespace App\Modules\Marketplace\Providers;

use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/marketplace.php');

        // Listeners
        $this->app['events']->listen(
            \App\Modules\Marketplace\Events\ReportGenerated::class,
            \App\Modules\Alerts\Listeners\SendNotification::class
        );
    }
}