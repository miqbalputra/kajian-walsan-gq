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
     * Retain inline scripts and Alpine evaluation for existing Livewire pages.
     * External executable assets are restricted to the application's CDNs.
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
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com",
                "style-src 'self' 'unsafe-inline'",
                "font-src 'self' data:",
                "img-src 'self' https: data: blob:",
                "connect-src 'self' https: wss:",
                "media-src 'self' blob:",
                "worker-src 'self' blob:",
                "frame-src 'self' blob:",
            ]),
        ];

        $response->headers->remove('X-Powered-By');

        // Vite's local development server uses a separate origin and WebSocket.
        if (app()->isLocal()) {
            unset($headers['Content-Security-Policy']);
        }

        if (app()->isProduction() && $request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000';
        }

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }
}
