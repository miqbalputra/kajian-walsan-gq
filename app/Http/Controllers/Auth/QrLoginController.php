<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\User;
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
        $key = 'qr-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'qr_code' => 'required|string|max:50',
        ]);

        $qrCode = $request->input('qr_code');

        // Validate QR Code format (WS-XXXXXXXX-YYYY)
        if (!preg_match('/^WS-[A-Z0-9]{8}-\d{4}$/', $qrCode)) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.',
            ], 400);
        }

        // Find parent by QR code
        $parent = ParentModel::where('qr_code_string', $qrCode)->first();

        if (!$parent) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak terdaftar.',
            ], 404);
        }

        // Check if user is active
        $user = $parent->user;

        if (!$user || !$user->is_active) {
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
        $key = 'qr-validate:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'valid' => false,
                'message' => "Terlalu banyak percobaan. Tunggu {$seconds} detik.",
            ], 429);
        }

        $request->validate([
            'qr_code' => 'required|string|max:50',
        ]);

        $qrCode = $request->input('qr_code');

        // Validate QR Code format
        if (!preg_match('/^WS-[A-Z0-9]{8}-\d{4}$/', $qrCode)) {
            RateLimiter::hit($key, 60);
            return response()->json([
                'valid' => false,
                'message' => 'Format QR Code tidak valid.',
            ]);
        }

        $parent = ParentModel::where('qr_code_string', $qrCode)->first();

        if (!$parent) {
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
}
