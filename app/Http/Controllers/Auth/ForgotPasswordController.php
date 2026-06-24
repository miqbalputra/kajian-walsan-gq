<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendPasswordResetWebhook;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
        ], [
            'identifier.required' => 'Masukkan username, email, atau nomor HP yang terdaftar.',
        ]);

        $key = 'forgot-password:' . $request->ip() . ':' . Str::lower($request->identifier);
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()
                ->withInput()
                ->withErrors(['identifier' => 'Terlalu banyak percobaan. Coba lagi beberapa menit lagi.']);
        }
        RateLimiter::hit($key, 300);

        $user = $this->findUser($request->identifier);

        // Selalu tampilkan pesan generik agar data akun tidak mudah ditebak.
        $statusMessage = 'Jika data cocok, instruksi reset password akan dikirim melalui email atau WhatsApp yang terdaftar.';

        if (!$user || !$user->is_active) {
            return back()->with('status', $statusMessage);
        }

        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // Dispatch pengiriman webhook ke queue agar request ke user langsung
        // selesai tanpa menunggu n8n merespons (sebelumnya sinkron sampai 15s).
        // Job punya retry otomatis (3x) + backoff 10s/30s/60s + logging gagal permanen.
        SendPasswordResetWebhook::dispatch(
            $user,
            $resetUrl,
            $request->identifier,
            (int) config('auth.passwords.users.expire', 60),
        );

        return back()->with('status', $statusMessage);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil diganti. Silakan login dengan password baru.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Link reset tidak valid atau sudah kedaluwarsa.']);
    }

    private function findUser(string $identifier): ?User
    {
        $identifier = trim($identifier);

        if (str_contains($identifier, '@')) {
            return User::where('is_active', true)
                ->whereRaw('LOWER(email) = ?', [Str::lower($identifier)])
                ->first();
        }

        $userByUsername = User::where('is_active', true)
            ->where('username', $identifier)
            ->first();

        if ($userByUsername) {
            return $userByUsername;
        }

        $phoneCandidates = $this->phoneCandidates($identifier);
        if ($phoneCandidates === []) {
            return null;
        }

        $normalizedPhoneSql = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, '+', ''), '-', ''), ' ', ''), '(', ''), ')', '')";

        return User::where('is_active', true)
            ->whereNotNull('phone')
            ->whereIn(DB::raw($normalizedPhoneSql), $phoneCandidates)
            ->first();
    }

    private function phoneCandidates(string $phone): array
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits || strlen($digits) < 8) {
            return [];
        }

        $local = $digits;
        if (str_starts_with($local, '62')) {
            $local = '0' . substr($local, 2);
        } elseif (str_starts_with($local, '8')) {
            $local = '0' . $local;
        }

        $international = $local;
        if (str_starts_with($international, '0')) {
            $international = '62' . substr($international, 1);
        }

        return array_values(array_unique([$digits, $local, $international]));
    }
}