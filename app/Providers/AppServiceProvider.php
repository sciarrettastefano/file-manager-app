<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        // Creo un gate per concedere qualsiasi permesso ai superadmin
        Gate::before(function (User $user) {
            if ($user->hasRole('superadmin')) {
                return true;
            }
        });
    }
}
