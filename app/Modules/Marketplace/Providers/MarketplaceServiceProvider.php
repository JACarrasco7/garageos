<?php

namespace App\Modules\Marketplace\Providers;

use App\Modules\Alerts\Listeners\SendNotification;
use App\Modules\Marketplace\Events\ReportGenerated;
use App\Modules\Marketplace\Models\MarketplaceListing;
use App\Modules\Marketplace\Policies\MarketplaceListingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/marketplace.php');

        // Register policies
        Gate::policy(
            MarketplaceListing::class,
            MarketplaceListingPolicy::class
        );

        // Listeners
        $this->app['events']->listen(
            ReportGenerated::class,
            SendNotification::class
        );
    }
}
