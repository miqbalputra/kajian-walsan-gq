<?php

use App\Listeners\FlushDompdfInstance;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value specifies the default Octane server that will be used when
    | starting the Octane server via the `octane:start` Artisan command.
    | Supported options: "frankenphp", "swoole", "roadrunner".
    |
    */

    'server' => env('OCTANE_SERVER', 'frankenphp'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When behind a reverse proxy (Coolify, Cloudflare, etc.), Octane needs
    | to trust the X-Forwarded-* headers to detect HTTPS. Set to true if
    | your production server is served via HTTPS.
    |
    */

    'https' => env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Octane Servers
    |--------------------------------------------------------------------------
    */

    'frankenphp' => [
        /*
        | The host and port FrankenPHP will listen on.
        */
        'host' => env('OCTANE_HOST', '0.0.0.0'),
        'port' => env('OCTANE_PORT', 80),

        /*
        | The number of FrankenPHP workers (defaults to CPU count).
        | On VPS 2 vCPU, 2-4 workers is a good starting point.
        | Each worker keeps a full Laravel app in memory.
        */
        'workers' => env('OCTANE_WORKERS', 2),

        /*
        | Recycle workers after N requests to prevent memory leaks.
        | DOMPDF and other packages may slowly accumulate memory.
        */
        'max_requests' => env('OCTANE_MAX_REQUESTS', 500),

        /*
        | Caddyfile path for custom FrankenPHP/Caddy configuration.
        | Null = use Octane's built-in Caddy config.
        */
        'caddyfile' => env('OCTANE_CADDYFILE', null),

        /*
        | Caddy server extra directives (appended to the Caddyfile).
        */
        'caddy' => [
            'extra_directives' => [],
        ],

        /*
        | Options for the FrankenPHP worker process.
        */
        'options' => [],
    ],

    'swoole' => [
        'host' => env('OCTANE_HOST', '127.0.0.1'),
        'port' => env('OCTANE_PORT', 8000),
        'workers' => env('OCTANE_WORKERS', 2),
        'task_workers' => env('OCTANE_TASK_WORKERS', 2),
        'max_requests' => env('OCTANE_MAX_REQUESTS', 500),
        'options' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Cache Store
    |--------------------------------------------------------------------------
    |
    | Octane uses a dedicated cache store for its internal locks and state.
    | Redis is recommended (already configured in your setup).
    |
    */

    'cache' => env('OCTANE_CACHE_STORE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Listeners
    |--------------------------------------------------------------------------
    |
    | Listeners are invoked during the Octane request lifecycle.
    | Use them to flush state, reset singletons, or perform cleanup.
    |
    */

    'listeners' => [
        WorkerStarting::class => [
            // ...actions on worker start
        ],

        RequestReceived::class => [
            // ...actions on each request received
        ],

        RequestTerminated::class => [
            // Flush DOMPDF singleton to prevent HTML/CSS leak between requests
            FlushDompdfInstance::class,
        ],

        WorkerStopping::class => [
            // ...actions on worker stop
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Warm Service Providers on Boot
    |--------------------------------------------------------------------------
    |
    | These service providers will be instantiated once when the worker boots,
    | keeping them in memory for all subsequent requests. Add providers that
    | are heavy to instantiate.
    |
    */

    'warm' => [
        // Kosong — biarkan Octane warm secara default.
        // Jangan pakai app()->getLoadedProviders() di sini karena
        // dapat break config:cache di environment Docker.
    ],

    /*
    |--------------------------------------------------------------------------
    | Flush State
    |--------------------------------------------------------------------------
    |
    | Octane will flush the following state between requests to prevent
    | data from leaking between requests in long-running workers.
    |
    */

    'flush' => [
        // ...bindings to flush between requests
    ],

    /*
    |--------------------------------------------------------------------------
    | Watch Config
    |--------------------------------------------------------------------------
    |
    | Watch these files for changes in development mode.
    | In production, Octane auto-reloads on code changes if file watching is on.
    |
    */

    'watch' => [
        'app/**/*.php',
        'config/**/*.php',
        'routes/**/*.php',
        'resources/views/**/*.blade.php',
        'public/js/**/*.js',
        'public/css/**/*.css',
    ],

];