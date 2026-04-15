<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\ParentModel;
use Laravel\Pulse\Facades\Pulse;
use Livewire\Blaze\Blaze;

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
        // Pulse Authorization
        Gate::define('viewPulse', function (User $user) {
            return config('pulse.enabled') && $user->isAdmin();
        });

        // Simple Pulse User Config
        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'extra' => $user->email,
        ]);

        // Livewire Blaze Optimization
        Blaze::optimize()
            ->in(resource_path('views/components'))
            ->in(resource_path('views/components/layouts'), compile: false);
    }
}
