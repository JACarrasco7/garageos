<?php

namespace App\Providers;

use App\Modules\Identity\Models\Garage;
use App\Policies\GaragePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Garage::class => GaragePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
