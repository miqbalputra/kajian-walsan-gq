<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth (Login Page - hanya untuk akun yang sudah link)
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['google' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }

        // Cari user berdasarkan google_id
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Coba cari berdasarkan email (jika email cocok, bisa link otomatis)
            $userByEmail = User::where('email', $googleUser->getEmail())->first();

            if ($userByEmail) {
                // Link akun yang sudah ada dengan Google
                $userByEmail->update([
                    'google_id'    => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                ]);
                $user = $userByEmail;
            } else {
                // Akun Google belum terdaftar & email tidak cocok - tolak
                return redirect()->route('login')->withErrors([
                    'google' => 'Akun Google ini belum terhubung. Silakan login dengan username & password terlebih dahulu, lalu hubungkan akun Google di halaman Profil.'
                ]);
            }
        }

        // Cek apakah akun aktif
        if (!$user->is_active) {
            return redirect()->route('login')->withErrors([
                'google' => 'Akun Anda tidak aktif. Hubungi administrator.'
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Link akun Google ke akun yang sudah login (dari halaman Profil)
     */
    public function linkRedirect()
    {
        return Socialite::driver('google')
            ->with(['state' => 'link_account'])
            ->redirect();
    }

    /**
     * Handle callback untuk link akun Google
     */
    public function linkCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Gagal menghubungkan akun Google. Coba lagi.');
        }

        $user = Auth::user();

        // Cek apakah google_id sudah dipakai akun lain
        $existing = User::where('google_id', $googleUser->getId())->where('id', '!=', $user->id)->first();
        if ($existing) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Akun Google ini sudah terhubung ke akun lain.');
        }

        $user->update([
            'google_id'    => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'email'        => $user->email ?? $googleUser->getEmail(),
        ]);

        return redirect()->route('wali-santri.profile')->with('google-success', 'Akun Google berhasil dihubungkan! Sekarang Anda bisa login dengan Google.');
    }

    /**
     * Unlink akun Google
     */
    public function unlink()
    {
        $user = Auth::user();

        if (!$user->password) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Tidak dapat melepas Google karena Anda tidak memiliki password. Atur password terlebih dahulu.');
        }

        $user->update(['google_id' => null, 'google_token' => null]);

        return redirect()->route('wali-santri.profile')->with('google-success', 'Akun Google berhasil dilepaskan.');
    }
}
