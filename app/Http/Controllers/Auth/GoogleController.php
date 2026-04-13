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

        // Enkripsi ID user ke parameter state.
        // Tujuannya agar jika request direbut oleh Antivirus/Pre-fetcher di background,
        // backend tetap tahu akun siapa yang harus dilink tanpa butuh auth session.
        $statePayload = encrypt(Auth::id() . '|' . time());

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

        if (!$targetUser) {
            return redirect()->route('login')->withErrors([
                'google' => 'Sesi Anda telah berakhir dan data state tidak valid. Silakan login ulang.'
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
                'error'        => $e->getMessage(),
                'callback_url' => $callbackUrl,
                'target_user'  => $targetUser->id
            ]);
            return redirect()->route('wali-santri.profile')->with('google-error', 'Gagal menghubungkan akun Google. Pastikan Client Secret di .env server sudah sesuai dengan Google Cloud. (Error: ' . $e->getMessage() . ')');
        }

        // 3. Proses linking normal (Token berhasil ditukar)
        // Cek apakah google_id sudah dipakai akun lain
        $existing = User::where('google_id', $googleUser->getId())->where('id', '!=', $targetUser->id)->first();
        if ($existing) {
            return redirect()->route('wali-santri.profile')->with('google-error', 'Akun Google ini sudah terhubung ke akun lain.');
        }

        $targetUser->update([
            'google_id'    => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'email'        => $targetUser->email ?? $googleUser->getEmail(),
        ]);

        Log::info('[Google Link] Success', ['user_id' => $targetUser->id, 'google_id' => $googleUser->getId()]);

        // Jika ini adalah request background dari antivirus (Auth::user() null), kita ga perlu pusing soal redirect
        // Karena browser aslinya nanti akan menyusul dan memicu catch blok di atas.
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

