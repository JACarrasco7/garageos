<?php

namespace App\Modules\Marketplace\Providers;

use App\Modules\Alerts\Listeners\SendNotification;
use App\Modules\Marketplace\Events\ReportGenerated;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/marketplace.php');

        // Listeners
        $this->app['events']->listen(
            ReportGenerated::class,
            SendNotification::class
        );
    }
}
