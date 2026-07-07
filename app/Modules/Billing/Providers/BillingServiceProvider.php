<?php

namespace App\Modules\Billing\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function ($app) {
            $secret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
            return new StripeClient($secret);
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/billing.php');
    }
}
