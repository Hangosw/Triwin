<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasAnyRole(['Super Admin', 'System Admin']) ? true : null;
        });

        if ($this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
}
