<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $sent = $this->sendToN8n($user, $resetUrl, $request->identifier);

        if (!$sent) {
            Log::warning('[ForgotPassword] n8n webhook not configured or failed', [
                'user_id' => $user->id,
            ]);
        }

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

    private function sendToN8n(User $user, string $resetUrl, string $identifier): bool
    {
        $webhookUrl = config('services.n8n.password_reset_webhook_url');
        if (!$webhookUrl) {
            return false;
        }

        $phone = $this->formatPhoneForWa($user->phone);
        $preferredChannel = str_contains($identifier, '@') ? 'email' : ($phone ? 'whatsapp' : 'email');

        try {
            $response = Http::timeout(15)->post($webhookUrl, [
                'secret' => config('services.n8n.password_reset_secret'),
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $phone,
                'preferred_channel' => $preferredChannel,
                'reset_url' => $resetUrl,
                'expires_minutes' => config('auth.passwords.users.expire', 60),
                'requested_at' => now()->toISOString(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('[ForgotPassword] n8n webhook failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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

    private function formatPhoneForWa(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, '08')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return str_starts_with($digits, '62') ? $digits : null;
    }
}
