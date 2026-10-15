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
     * Socialite session state remains enabled so the callback is bound to the
     * browser session and protected from login CSRF.
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
                ->user();
        } catch (\Throwable $e) {
            Log::error('[Google Login] Callback failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'code' => $e->getCode(),
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
     * The authenticated browser session is used as the link target. The
     * Socialite state value binds the provider callback to that session.
     */
    public function linkRedirect()
    {
        if (! $this->hasGoogleConfig()) {
            Log::error('[Google Link] Missing Google OAuth credentials', ['user_id' => Auth::id()]);

            return redirect()->route('wali-santri.profile')->with('google-error', 'Konfigurasi Google Login belum lengkap. Hubungi administrator.');
        }

        $callbackUrl = $this->getLinkCallbackUrl();

        return Socialite::driver('google')
            ->redirectUrl($callbackUrl)
            ->redirect();
    }

    /**
     * Handle callback untuk link akun Google.
     */
    public function linkCallback(Request $request)
    {
        $callbackUrl = $this->getLinkCallbackUrl();

        $targetUser = Auth::user();

        if (! $targetUser) {
            return redirect()->route('login')->withErrors([
                'google' => 'Sesi Anda telah berakhir dan data state tidak valid. Silakan login ulang.',
            ]);
        }

        // 2. Coba tukarkan Authorization Code dengan Google token
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($callbackUrl)
                ->user();
        } catch (\Throwable $e) {
            Log::error('[Google Link] Callback failed', [
                'error' => $e->getMessage(),
                'callback_url' => $callbackUrl,
                'target_user' => $targetUser->id,
            ]);

            return redirect()->route('wali-santri.profile')->with('google-error', 'Gagal menghubungkan akun Google. Silakan mulai kembali dari halaman profil.');
        }

        // 3. Proses linking normal (Token berhasil ditukar)
        $googleEmail = Str::lower(trim((string) $googleUser->getEmail()));
        if ($googleEmail === '') {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Akun Google tidak mengirim alamat email. Pastikan izin email aktif, lalu coba lagi.');
        }

        if ($targetUser->isWaliSantri() && ! User::isGmailAddress($googleEmail)) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Untuk akun wali santri, hubungkan akun Gmail dengan alamat yang berakhiran @gmail.com.');
        }

        // Cek apakah google_id sudah dipakai akun lain
        $existing = User::where('google_id', $googleUser->getId())->where('id', '!=', $targetUser->id)->first();
        if ($existing) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Akun Google ini sudah terhubung ke akun lain.');
        }

        // The Google account is linked while the user is authenticated, so its
        // provider email can safely replace the temporary/imported address.
        $emailOwner = User::whereRaw('LOWER(email) = ?', [$googleEmail])
            ->where('id', '!=', $targetUser->id)
            ->exists();
        if ($emailOwner) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Email Google ini sudah digunakan akun lain di aplikasi. Hubungi admin jika ini akun Bapak/Ibu.');
        }

        $targetUser->update([
            'google_id' => $googleUser->getId(),
            'email' => $googleEmail,
        ]);

        Log::info('[Google Link] Success', ['user_id' => $targetUser->id, 'google_id' => $googleUser->getId()]);

        return redirect()->route('wali-santri.profile')->with('google-success', 'Gmail berhasil disimpan dan akun Google terhubung. Sekarang Bapak/Ibu bisa login dengan Google.');
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
