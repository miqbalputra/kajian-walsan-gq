<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class QrLoginController extends Controller
{
    /**
     * Show the QR login scanner page.
     */
    public function showScanner()
    {
        return view('auth.qr-login');
    }

    /**
     * Process QR code login.
     */
    public function login(Request $request)
    {
        // Rate limiting - max 5 attempts per minute per IP
        $key = 'qr-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'qr_code' => 'required|string|max:100',
        ]);

        $qrCode = trim($request->input('qr_code'));

        if (! $this->isValidQrPayload($qrCode)) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.',
            ], 400);
        }

        // Find parent by current QR payload. Supports legacy WS-* and current A/B/T + NIS codes.
        $parent = ParentModel::where('qr_code_string', $qrCode)->first();

        if (! $parent) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak terdaftar.',
            ], 404);
        }

        // Check if user is active
        $user = $parent->user;

        if (! $user || ! $user->is_active) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif. Silakan hubungi admin.',
            ], 403);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        // Login the user
        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => route('dashboard'),
            'user' => [
                'name' => $user->name,
                'type' => $parent->type_display,
            ],
        ]);
    }

    /**
     * Validate QR code without logging in.
     */
    public function validate(Request $request)
    {
        // Rate limiting for validation endpoint
        $key = 'qr-validate:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'valid' => false,
                'message' => "Terlalu banyak percobaan. Tunggu {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'qr_code' => 'required|string|max:100',
        ]);

        $qrCode = trim($request->input('qr_code'));

        if (! $this->isValidQrPayload($qrCode)) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'valid' => false,
                'message' => 'Format QR Code tidak valid.',
            ]);
        }

        $parent = ParentModel::where('qr_code_string', $qrCode)->first();

        if (! $parent) {
            RateLimiter::hit($key, 60);

            return response()->json([
                'valid' => false,
                'message' => 'QR Code tidak valid.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'parent' => [
                'name' => $parent->user->name,
                'type' => $parent->type_display,
                'children_count' => $parent->students()->count(),
            ],
        ]);
    }

    /**
     * Accept stored QR payloads, not only old WS-* format.
     */
    private function isValidQrPayload(string $qrCode): bool
    {
        return $qrCode !== ''
            && strlen($qrCode) <= 100
            && (bool) preg_match('/^[A-Za-z0-9:_\-]+$/', $qrCode);
    }
}
