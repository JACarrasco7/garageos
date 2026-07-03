<?php

namespace App\Providers;

use App\Modules\Identity\Models\Garage;
use App\Modules\Marketplace\Models\Transaction;
use App\Modules\Providers\Models\Provider;
use App\Policies\GaragePolicy;
use App\Policies\ProviderPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Garage::class => GaragePolicy::class,
        Transaction::class => TransactionPolicy::class,
        Provider::class => ProviderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
