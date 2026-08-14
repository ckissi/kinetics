<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        // The single place that decides whether a visitor may see Pro content.
        // Guests never can, which is why the closure takes a nullable user.
        Gate::define('view-pro-content', fn(?User $user) => $user?->hasProAccess() ?? false);
    }
}
