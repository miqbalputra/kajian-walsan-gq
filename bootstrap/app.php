<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies: required for HTTPS + Coolify reverse proxy
        // Without this, Livewire upload CSRF fails with 419 in production
        $middleware->trustProxies(at: '*');

        // Inject PWA install prompt at response level so it still appears if a Blade view is stale.
        $middleware->web(append: [
            \App\Http\Middleware\InjectPwaInstallPrompt::class,
        ]);

        // Register CheckRole middleware alias for RBAC
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'hermes.agent' => \App\Http\Middleware\VerifyHermesAgent::class,
        ]);

        // Exclude internal API endpoints from CSRF
        $middleware->validateCsrfTokens(except: [
            '/internal-reset-password',
            '/hermes-agent',
            '/hermes-agent/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
