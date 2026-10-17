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
        \Illuminate\Support\Facades\Auth::provider('multi_table', function ($app, array $config) {
            return new \App\Auth\MultiTableUserProvider();
        });

        // Super Admin Full Access
        \Illuminate\Support\Facades\Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
        });

        // Role Gates
        \Illuminate\Support\Facades\Gate::define('admin-access', function ($user) {
            return method_exists($user, 'isAdmin') && $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('panitia-access', function ($user) {
            return method_exists($user, 'hasRole') && $user->hasRole(['admin', 'panitia']);
        });

        \Illuminate\Support\Facades\Gate::define('murid-access', function ($user) {
            return method_exists($user, 'isMurid') && $user->isMurid();
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

        // Global View Composer untuk Logo Sekolah & Pengaturan Sistem ke Seluruh Views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('pengaturan_sistem')) {
                    $globalSettings = \App\Models\SystemSetting::pluck('value', 'key')->toArray();
                    $view->with('globalSettings', $globalSettings);
                    $view->with('schoolLogo', $globalSettings['school_logo'] ?? null);
                    $view->with('copImageBanner', $globalSettings['cop_image_banner'] ?? null);
                }
            } catch (\Throwable $e) {
                // Abaikan saat awal migrasi
            }
        });
    }
}
