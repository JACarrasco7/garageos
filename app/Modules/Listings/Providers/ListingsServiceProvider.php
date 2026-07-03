<?php

namespace App\Modules\Listings\Providers;

use App\Modules\Listings\Services\ListingService;
use App\Modules\Listings\Services\Parsers\OpenGraphParser;
use Illuminate\Support\ServiceProvider;

class ListingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag([
            OpenGraphParser::class,
        ], 'listing.parser');

        $this->app->bind(
            ListingService::class,
            function ($app) {
                return new ListingService(
                    $app->tagged('listing.parser')
                );
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // No migrations - Listings module uses Marketplace migrations
    }
}
