<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth (Login page)
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
            Log::error('[Google Login] Callback failed', ['error' => $e->getMessage()]);
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
     * Helper: Dapatkan URL callback untuk link flow.
     */
    private function getLinkCallbackUrl()
    {
        // Secara eksplisit paksakan URL HTTPS agar 100% cocok dengan Google Console
        return rtrim(config('app.url'), '/') . '/auth/google/link/callback';
    }

    /**
     * Link akun Google ke akun yang sudah login (dari halaman Profil)
     * Menggunakan stateless() karena user sudah authenticated —
     * tidak perlu CSRF state check dari Socialite.
     */
    public function linkRedirect()
    {
        $callbackUrl = $this->getLinkCallbackUrl();

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->stateless()
            ->redirect();
    }

    /**
     * Handle callback untuk link akun Google (stateless)
     */
    public function linkCallback(Request $request)
    {
        $callbackUrl = $this->getLinkCallbackUrl();

        // LOGGING UNTUK DETEKSI DOUBLE REQUEST
        Log::info('[Google Link] Callback hit', [
            'code_preview' => substr($request->code, 0, 10),
            'auth_user'    => Auth::id(),
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent()
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            $serverTime = now()->format('Y-m-d H:i:s P');
            Log::error('[Google Link] Callback failed', [
                'error'        => $e->getMessage(),
                'callback_url' => $callbackUrl,
                'server_time'  => $serverTime,
                'request_code' => $request->code ? 'present' : 'missing',
            ]);
            return redirect()->route('wali-santri.profile')->with('google-error', "Gagal menghubungkan akun Google: {$e->getMessage()}. (Info Debug: Pastikan jam server VPS akurat. Jam server saat ini: {$serverTime}. URL: {$callbackUrl})");
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'google' => 'Sesi Anda telah berakhir. Silakan login ulang lalu hubungkan Google.'
            ]);
        }

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

        Log::info('[Google Link] Success', ['user_id' => $user->id, 'google_id' => $googleUser->getId()]);

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

