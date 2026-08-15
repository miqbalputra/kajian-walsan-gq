<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Models\UserLoginAlias;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Custom authentication logic - support username or email
        Fortify::authenticateUsing(function (Request $request) {
            $login = trim((string) $request->input('username'));
            $normalizedLogin = Str::lower($login);

            $user = User::where(function ($query) use ($login) {
                $query->whereRaw('LOWER(username) = ?', [Str::lower($login)])
                    ->orWhereRaw('LOWER(email) = ?', [Str::lower($login)]);
            })
                ->where('is_active', true)
                ->first();

            $alias = null;
            if (! $user) {
                $alias = UserLoginAlias::query()
                    ->active()
                    ->with('user')
                    ->whereRaw('LOWER(username) = ?', [$normalizedLogin])
                    ->first();
                $user = $alias?->user;
                if ($user && ! $user->is_active) {
                    $user = null;
                }
            }

            if ($user) {
                // Check if password is a valid bcrypt hash before attempting verification
                $passwordInfo = password_get_info($user->password);
                if ($passwordInfo['algo'] === null || $passwordInfo['algoName'] === 'unknown') {
                    // Password is not properly hashed - reject silently instead of crashing
                    return null;
                }

                $passwordHash = $alias?->password ?? $user->password;
                if (Hash::check($request->password, $passwordHash)) {
                    return $user;
                }
            }

            return null;
        });

        // Login view
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Register view
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Forgot Password view
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        // Reset Password view
        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        // Rate limiting
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('mustawa1-public-form', function (Request $request) {
            return Limit::perMinutes(15, 10)->by('mustawa1-public-form:'.$request->ip());
        });
    }
}
