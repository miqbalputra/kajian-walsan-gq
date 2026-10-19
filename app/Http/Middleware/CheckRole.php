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

        $user = auth()->user()->fresh();
        abort_unless($user?->is_active, 403, 'Akun ini tidak aktif atau sudah diarsipkan.');
        abort_if($user->parentProfile()->where('type', 'teacher')->whereNotNull('teacher_archived_at')->exists(), 403, 'Akun guru sudah diarsipkan.');

        // Check if user has any of the allowed roles
        foreach ($roles as $role) {
            // Support comma-separated roles
            $roleList = explode(',', $role);
            foreach ($roleList as $r) {
                $allowed = match (trim($r)) {
                    'wali_kelas' => $user->isWaliKelas(),
                    'guru' => $user->isGuru(),
                    'wali_santri' => $user->isWaliSantri(),
                    default => $user->hasRole(trim($r)),
                };
                if ($allowed) {
                    return $next($request);
                }
            }
        }

        // User doesn't have the required role
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
