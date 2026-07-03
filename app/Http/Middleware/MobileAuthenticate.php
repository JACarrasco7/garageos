<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MobileAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent();
        $isMobile = $userAgent && preg_match('/mobile|android|iphone|ipad/i', $userAgent);
        $isMobileHeader = $request->header('X-Mobile') === 'true';

        if (! $isMobile && ! $isMobileHeader) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
