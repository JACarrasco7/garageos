<?php

namespace App\Modules\Identity\Providers;

use App\Modules\Identity\Models\Garage;
use App\Modules\Identity\Policies\GaragePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class IdentityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Migrations are in the root database/migrations directory
        Gate::policy(Garage::class, GaragePolicy::class);

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../Routes/identity.php');
        });
    }

    public function register(): void
    {
        //
    }

    protected function routeConfiguration(): array
    {
        return [
            'middleware' => ['web', 'auth'],
        ];
    }
}
