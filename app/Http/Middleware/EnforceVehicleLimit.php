<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceVehicleLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // If user has an active subscription, check plan limits
        if ($user->subscribed('default')) {
            $plan = config('subscription.plans.' . $user->subscription('default')->stripe_price);

            if ($plan && $plan['vehicle_limit'] !== null) {
                $vehicleCount = $user->garages()->withCount('vehicles')->get()->sum('vehicles_count');

                if ($vehicleCount >= $plan['vehicle_limit']) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Has alcanzado el límite de vehículos de tu plan. Actualiza tu suscripción para añadir más.',
                        ], 403);
                    }

                    return redirect()->route('subscription.index')
                        ->with('error', 'Has alcanzado el límite de vehículos de tu plan (' . $plan['vehicle_limit'] . '). Actualiza tu suscripción para añadir más.');
                }
            }
        }

        return $next($request);
    }
}
