<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust only explicitly configured reverse proxies. Do not use '*':
        // otherwise clients can spoof X-Forwarded-Proto/Host and influence
        // secure URL and cookie decisions.
        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', ''))
        )));

        // Coolify terminates TLS in a private Docker network before the
        // request reaches this container. PRIVATE_SUBNETS covers that
        // managed hop (including the local nginx/FPM hop) without trusting
        // arbitrary public clients when TRUSTED_PROXIES is omitted.
        if (blank($trustedProxies)) {
            $trustedProxies = ['PRIVATE_SUBNETS'];
        }

        if (filled($trustedProxies)) {
            $middleware->trustProxies(at: $trustedProxies);
        }

        // Inject PWA install prompt at response level so it still appears if a Blade view is stale.
        $middleware->web(append: [
            \App\Http\Middleware\InjectPwaInstallPrompt::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Register CheckRole middleware alias for RBAC
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'hermes.agent' => \App\Http\Middleware\VerifyHermesAgent::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Never flash credentials, tokens, uploaded files, or integration
        // secrets back into the session after validation failures.
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'secret',
            'qr_code',
            'proof_file',
            'proof_photo',
        ]);

        // Keep API consumers on a stable, non-sensitive error contract in
        // production while preserving the correlation ID for log lookup.
        $exceptions->respond(function ($response, \Throwable $exception, Request $request) {
            $requestId = $request->attributes->get('request_id') ?: (string) Str::uuid();
            $response->headers->set('X-Request-ID', $requestId);

            if ($request->expectsJson() && $response->isServerError() && ! config('app.debug')) {
                return response()->json([
                    'message' => 'Terjadi kesalahan internal. Silakan coba lagi atau hubungi administrator.',
                    'request_id' => $requestId,
                ], $response->getStatusCode(), [
                    'X-Request-ID' => $requestId,
                ]);
            }

            return $response;
        });
    })->create();
