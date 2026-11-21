<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        // TrustProxies must run first. Keep the internal health probe available.
        $origin = rtrim((string) config('app.url'), '/');
        if (app()->isProduction() && str_starts_with($origin, 'https://')
            && ! $request->isSecure() && ! $request->is('up')) {
            // Use the configured origin, never an untrusted Host header.
            return redirect()->away($origin.$request->getRequestUri(), $request->isMethodSafe() ? 301 : 308);
        }

        return $next($request);
    }
}
