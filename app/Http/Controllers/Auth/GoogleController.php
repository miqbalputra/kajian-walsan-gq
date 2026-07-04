<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Helper: Dapatkan URL callback untuk login flow.
     */
    private function getLoginCallbackUrl()
    {
        return $this->buildCallbackUrl(config('services.google.redirect'), '/auth/google/callback');
    }

    /**
     * Redirect ke Google OAuth (Login page)
     *
     * Menggunakan stateless() untuk kompatibilitas dengan Laravel Octane.
     * redirectUrl() dipanggil eksplisit agar redirect_uri di authorize request
     * dan token exchange request identik, menghindari Google 400 Bad Request.
     */
    public function redirect()
    {
        if (! $this->hasGoogleConfig()) {
            Log::error('[Google Login] Missing Google OAuth credentials');

            return redirect()->route('login')->withErrors([
                'google' => 'Konfigurasi Google Login belum lengkap. Hubungi administrator.',
            ]);
        }

        $callbackUrl = $this->getLoginCallbackUrl();

        $redirectUrl = Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->scopes(['openid', 'profile', 'email'])
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        Log::info('[Google Login] Generated redirect URL', [
            'callback_url' => $callbackUrl,
            'target_url' => $redirectUrl,
        ]);

        return response()
            ->view('auth.google-redirect', ['redirectUrl' => $redirectUrl])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Handle callback dari Google OAuth (Login Page - hanya untuk akun yang sudah link)
     */
    public function callback()
    {
        $callbackUrl = $this->getLoginCallbackUrl();

        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            Log::error('[Google Login] Callback failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'request_state' => request()->get('state'),
                'request_code' => request()->has('code') ? 'present' : 'missing',
                'redirect_uri_config' => config('services.google.redirect'),
                'redirect_uri_used' => $callbackUrl,
            ]);

            return redirect()->route('login')->withErrors(['google' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }

        $googleEmail = Str::lower(trim((string) $googleUser->getEmail()));

        if ($googleEmail === '') {
            return redirect()->route('login')->withErrors([
                'google' => 'Akun Google tidak mengirim alamat email. Pastikan izin email aktif di Google OAuth.',
            ]);
        }

        try {
            $user = $this->findLoginUser($googleUser->getId(), $googleEmail);
        } catch (\Throwable $e) {
            Log::error('[Google Login] User lookup failed', [
                'error' => $e->getMessage(),
                'email' => $googleEmail,
            ]);

            return redirect()->route('login')->withErrors([
                'google' => 'Login Google belum bisa diproses. Jalankan migration database lalu coba lagi.',
            ]);
        }

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'google' => 'Email Google ini belum terdaftar di aplikasi. Pastikan email Gmail sudah disimpan di akun wali santri.',
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'google' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function findLoginUser(string $googleId, string $googleEmail): ?User
    {
        if (! $this->hasGoogleColumns()) {
            throw new \RuntimeException('Missing users.google_id column');
        }

        $user = User::where('google_id', $googleId)->first();
        if ($user) {
            if (Str::lower(trim((string) $user->email)) !== $googleEmail) {
                Log::warning('[Google Login] google_id matched but email is different', [
                    'user_id' => $user->id,
                    'google_email' => $googleEmail,
                ]);

                return null;
            }

            return $user;
        }

        $user = User::whereRaw('LOWER(email) = ?', [$googleEmail])->first();
        if (! $user) {
            return null;
        }

        if ($user->google_id && $user->google_id !== $googleId) {
            Log::warning('[Google Login] Email matched but google_id is different', [
                'user_id' => $user->id,
            ]);

            return null;
        }

        $user->update([
            'google_id' => $googleId,
        ]);

        return $user;
    }

    private function hasGoogleColumns(): bool
    {
        return Schema::hasColumn('users', 'google_id');
    }

    /**
     * Helper: Dapatkan URL callback untuk link flow.
     */
    private function getLinkCallbackUrl()
    {
        return $this->buildCallbackUrl(config('services.google.link_redirect'), '/auth/google/link/callback');
    }

    /**
     * Link akun Google ke akun yang sudah login (dari halaman Profil)
     * Menggunakan stateless() karena user sudah authenticated —
     * tidak perlu CSRF state check dari Socialite.
     */
    public function linkRedirect()
    {
        if (! $this->hasGoogleConfig()) {
            Log::error('[Google Link] Missing Google OAuth credentials', ['user_id' => Auth::id()]);

            return redirect()->route('wali-santri.profile')->with('google-error', 'Konfigurasi Google Login belum lengkap. Hubungi administrator.');
        }

        $callbackUrl = $this->getLinkCallbackUrl();

        // Enkripsi ID user ke parameter state.
        // Tujuannya agar jika request direbut oleh Antivirus/Pre-fetcher di background,
        // backend tetap tahu akun siapa yang harus dilink tanpa butuh auth session.
        $statePayload = encrypt(Auth::id().'|'.time());

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->with(['state' => $statePayload])
            ->stateless()
            ->redirect();
    }

    /**
     * Handle callback untuk link akun Google (stateless)
     */
    public function linkCallback(Request $request)
    {
        $callbackUrl = $this->getLinkCallbackUrl();

        // 1. Ekstrak User ID dari state jika ada
        $intendedUserId = null;
        if ($request->has('state')) {
            try {
                $decrypted = decrypt($request->state);
                $intendedUserId = explode('|', $decrypted)[0] ?? null;
            } catch (\Throwable $e) {
                // Abaikan jika tidak valid
            }
        }

        // Tentukan target fallback user (dari Auth session atau dari state rahasia)
        $targetUser = Auth::user() ?? ($intendedUserId ? User::find($intendedUserId) : null);

        if (! $targetUser) {
            return redirect()->route('login')->withErrors([
                'google' => 'Sesi Anda telah berakhir dan data state tidak valid. Silakan login ulang.',
            ]);
        }

        // 2. Coba tukarkan Authorization Code dengan Google token
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            // DETEKSI DOUBLE REQUEST:
            // Jika token exchange gagal (biasanya karena invalid_grant)
            // KITA CEK apakah akun user ternyata BARUSAJA berhasil di-link dalam 2 menit terakhir!
            // Jika iya, artinya proses pertama (Antivirus) berhasil, dan ini adalah proses kedua (Browser).
            if (
                str_contains($e->getMessage(), 'invalid_grant') &&
                $targetUser->google_id !== null &&
                $targetUser->updated_at->diffInSeconds(now()) < 120
            ) {
                return redirect()->route('wali-santri.profile')->with('google-success', 'Akun Google berhasil dihubungkan! (Diselamatkan dari interupsi keamanan browser)');
            }

            // Jika error lain atau belum terlink sama sekali, tampilkan error aslinya
            Log::error('[Google Link] Callback failed', [
                'error' => $e->getMessage(),
                'callback_url' => $callbackUrl,
                'target_user' => $targetUser->id,
            ]);

            return redirect()->route('wali-santri.profile')->with('google-error', 'Gagal menghubungkan akun Google. Pastikan Client Secret di .env server sudah sesuai dengan Google Cloud. (Error: '.$e->getMessage().')');
        }

        // 3. Proses linking normal (Token berhasil ditukar)
        // Cek apakah google_id sudah dipakai akun lain
        $existing = User::where('google_id', $googleUser->getId())->where('id', '!=', $targetUser->id)->first();
        if ($existing) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Akun Google ini sudah terhubung ke akun lain.');
        }

        $targetUser->update([
            'google_id' => $googleUser->getId(),
            'email' => $targetUser->email ?? $googleUser->getEmail(),
        ]);

        Log::info('[Google Link] Success', ['user_id' => $targetUser->id, 'google_id' => $googleUser->getId()]);

        // Jika ini adalah request background dari antivirus (Auth::user() null), kita ga perlu pusing soal redirect
        // Karena browser aslinya nanti akan menyusul dan memicu catch blok di atas.
        return redirect()->route('wali-santri.profile')->with('google-success', 'Akun Google berhasil dihubungkan! Sekarang Anda bisa login dengan Google.');
    }

    private function buildCallbackUrl(?string $configuredUrl, string $fallbackPath): string
    {
        $configuredUrl = trim((string) $configuredUrl);

        if (Str::startsWith($configuredUrl, ['http://', 'https://'])) {
            return $configuredUrl;
        }

        $path = $configuredUrl !== '' ? $configuredUrl : $fallbackPath;

        if (! Str::startsWith($path, '/')) {
            $path = '/'.$path;
        }

        return rtrim(config('app.url'), '/').$path;
    }

    private function hasGoogleConfig(): bool
    {
        $clientId = trim((string) config('services.google.client_id'));
        $clientSecret = trim((string) config('services.google.client_secret'));

        return $clientId !== ''
            && $clientSecret !== ''
            && ! Str::startsWith($clientId, 'your_')
            && ! Str::startsWith($clientSecret, 'your_');
    }

    /**
     * Unlink akun Google
     */
    public function unlink()
    {
        $user = Auth::user();

        if (! $user->password) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Tidak dapat melepas Google karena Anda tidak memiliki password. Atur password terlebih dahulu.');
        }

        $user->update(['google_id' => null]);

        return redirect()->route('wali-santri.profile')->with('google-success', 'Akun Google berhasil dilepaskan.');
    }
}
