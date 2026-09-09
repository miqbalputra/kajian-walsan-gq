<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add safe baseline response headers and a server-side correlation ID.
     * CSP is intentionally not forced here because the existing application
     * still uses inline scripts and trusted CDN assets; it should be added
     * after a nonce/hash migration is completed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);

        $response = $next($request);

        $headers = [
            'X-Request-ID' => $requestId,
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(self), geolocation=(), microphone=()',
        ];

        if (app()->isProduction() && $request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000';
        }

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }
}
