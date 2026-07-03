<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitApiMiddleware
{
    public function handle(Request $request, Closure $next, string $key = 'api'): mixed
    {
        $executed = RateLimiter::attempt(
            $key.':'.$request->ip(),
            $key === 'api' ? 60 : 10,
            function () {}
        );

        if (! $executed) {
            return response()->json([
                'error' => 'Too many requests',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return $next($request);
    }
}
