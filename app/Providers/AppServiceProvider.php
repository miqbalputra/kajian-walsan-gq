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
        // Hanya admin yang bisa mengakses dashboard /pulse
        // ===================================================
        Gate::define('viewPulse', function (User $user) {
            return $user->isAdmin();
        });

        // Konfigurasi tampilan user di Pulse dashboard
        Pulse::user(fn ($user) => [
            'name'   => $user->name,
            'extra'  => $user->role?->display_name ?? 'Unknown Role',
            'avatar' => $user->avatar ?? null,
        ]);

        // ===================================================
        // Livewire Blaze: Optimasi rendering Blade components
        // Menggunakan Function Compiler (default) untuk semua
        // anonymous components di folder views/components.
        // Layout (class-based) otomatis tidak terpengaruh.
        // pwa-push & pwa-install TIDAK pakai fold karena
        // menggunakan csrf_token() & config() (global state).
        // ===================================================
        Blaze::optimize()
            ->in(resource_path('views/components'))
            ->in(resource_path('views/components/layouts'), compile: false);
    }
}
