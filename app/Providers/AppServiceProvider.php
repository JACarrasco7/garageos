<?php

namespace App\Providers;

use App\Listeners\AssignRoleOnSubscription;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(PermissionServiceProvider::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Escuchar evento de login para asignar roles
        Event::listen(Login::class, AssignRoleOnSubscription::class);
    }
}
