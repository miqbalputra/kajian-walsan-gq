<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Blaze\Blaze;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_ALL, 'id_ID.utf8', 'id_ID', 'id');

        // ===================================================
        // Laravel Pulse: Monitoring & Application Insights
        // ===================================================
        Gate::define('viewPulse', function (User $user) {
            return config('pulse.enabled') && $user->isAdmin();
        });

        // Konfigurasi tampilan user di Pulse dashboard
        if ($this->app->bound(Pulse::class)) {
            Pulse::user(fn ($user) => [
                'name'   => $user->name,
                'extra'  => $user->role?->display_name ?? 'User',
                'avatar' => $user->avatar ?? null,
            ]);
        }

        // ===================================================
        // Livewire Blaze: Optimasi rendering Blade components
        // ===================================================
        Blaze::optimize()
            ->in(resource_path('views/components'))
            ->in(resource_path('views/components/layouts'), compile: false);
    }
}
