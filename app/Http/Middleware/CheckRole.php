<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Allowed roles (comma-separated or multiple arguments)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has any of the allowed roles
        foreach ($roles as $role) {
            // Support comma-separated roles
            $roleList = explode(',', $role);
            foreach ($roleList as $r) {
                if ($user->hasRole(trim($r))) {
                    return $next($request);
                }
            }
        }

        // User doesn't have the required role
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
