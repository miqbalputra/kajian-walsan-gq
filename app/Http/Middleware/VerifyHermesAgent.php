<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHermesAgent
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredSecret = config('hermes.secret');

        if (! filled($configuredSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'Hermes Agent API belum dikonfigurasi.',
            ], 503);
        }

        $providedSecret = $request->header('X-Hermes-Secret')
            ?: $request->bearerToken()
            ?: $request->input('secret');

        if (! is_string($providedSecret) || ! hash_equals($configuredSecret, $providedSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized Hermes Agent.',
            ], 403);
        }

        return $next($request);
    }
}
