<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');

        if (app()->environment('local')) {
            $csp = "default-src 'self'; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 http://localhost:5174 http://127.0.0.1:5173 http://127.0.0.1:5174 https://js.sentry-cdn.com; "
                ."style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; "
                ."img-src 'self' data: https: http:; "
                ."font-src 'self' https: data:; "
                ."connect-src 'self' http://localhost:5173 http://localhost:5174 http://127.0.0.1:5173 http://127.0.0.1:5174 ws://localhost:5173 ws://localhost:5174 ws://127.0.0.1:5173 ws://127.0.0.1:5174 wss: https://sentry.io; "
                ."frame-ancestors 'none';";
        } else {
            $csp = "default-src 'self'; "
                ."script-src 'self' 'unsafe-inline' https://js.sentry-cdn.com; "
                ."style-src 'self' 'unsafe-inline'; "
                ."img-src 'self' data: https: http:; "
                ."font-src 'self' https:; "
                ."connect-src 'self' https://sentry.io wss:; "
                ."frame-ancestors 'none';";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
