<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasFeature($feature)) {
            return redirect()->route('subscription.index')
                ->with('error', "Necesitas un plan superior para acceder a esta funcionalidad.");
        }

        return $next($request);
    }
}
