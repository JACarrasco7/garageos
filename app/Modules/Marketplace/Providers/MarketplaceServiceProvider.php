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

        // Register policies
        \Illuminate\Support\Facades\Gate::policy(
            \App\Modules\Marketplace\Models\MarketplaceListing::class,
            \App\Modules\Marketplace\Policies\MarketplaceListingPolicy::class
        );

        // Listeners
        $this->app['events']->listen(
            ReportGenerated::class,
            SendNotification::class
        );
    }
}
