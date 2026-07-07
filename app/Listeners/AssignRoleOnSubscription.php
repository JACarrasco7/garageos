<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class AssignRoleOnSubscription implements ShouldQueue
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Limpiar roles antiguos
        $user->syncRoles(['user']);

        // Asignar rol según suscripción
        $plan = $user->subscription('default')?->stripe_price;

        if ($plan === 'importer') {
            $user->assignRole('importer');
        } elseif ($plan === 'pro') {
            // Mantener rol user
        } else {
            // Usuario sin suscripción pagada
            $user->assignRole('user');
        }
    }
}
