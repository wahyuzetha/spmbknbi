<?php

namespace App\Providers;

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
        // Super Admin Full Access
        \Illuminate\Support\Facades\Gate::before(function (\App\Models\User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Role Gates
        \Illuminate\Support\Facades\Gate::define('admin-access', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('panitia-access', function (\App\Models\User $user) {
            return $user->hasRole(['admin', 'panitia']);
        });

        \Illuminate\Support\Facades\Gate::define('murid-access', function (\App\Models\User $user) {
            return $user->isMurid();
        });

        // Feature Specific Gates
        \Illuminate\Support\Facades\Gate::define('verify-documents', function (\App\Models\User $user) {
            return $user->hasRole(['admin', 'panitia']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-financial', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manage-settings', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('view-reports', function (\App\Models\User $user) {
            return $user->hasRole(['admin', 'panitia']);
        });
    }
}
