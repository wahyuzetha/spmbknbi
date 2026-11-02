<?php

namespace App\Providers;

use App\Auth\MultiTableUserProvider;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        Auth::provider('multi_table', function ($app, array $config) {
            return new MultiTableUserProvider;
        });

        // Super Admin Full Access
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
        });

        // Role Gates
        Gate::define('admin-access', function ($user) {
            return method_exists($user, 'isAdmin') && $user->isAdmin();
        });

        Gate::define('panitia-access', function ($user) {
            return method_exists($user, 'hasRole') && $user->hasRole(['admin', 'panitia']);
        });

        Gate::define('murid-access', function ($user) {
            return method_exists($user, 'isMurid') && $user->isMurid();
        });

        // Feature Specific Gates
        Gate::define('verify-documents', function (User $user) {
            return $user->hasRole(['admin', 'panitia']);
        });

        Gate::define('manage-financial', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-reports', function (User $user) {
            return $user->hasRole(['admin', 'panitia']);
        });

        // Global View Composer untuk Logo Sekolah & Pengaturan Sistem ke Seluruh Views (Memoized & Cached)
        View::composer('*', function ($view) {
            try {
                $globalSettings = SystemSetting::allCached();
                $view->with('globalSettings', $globalSettings);
                $view->with('schoolLogo', $globalSettings['school_logo'] ?? null);
                $view->with('copImageBanner', $globalSettings['cop_image_banner'] ?? null);
            } catch (\Throwable $e) {
                // Abaikan saat awal migrasi
            }
        });
    }
}
