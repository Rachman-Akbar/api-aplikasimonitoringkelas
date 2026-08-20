<?php

namespace App\Providers;

use App\Filament\Http\Responses\Auth\LogoutResponse as CustomLogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Schema;
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
        // Fix for MySQL "Specified key was too long" error
        Schema::defaultStringLength(191);

        $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

        // Bind custom logout response for Filament
        $this->app->bind(
            LogoutResponseContract::class,
            CustomLogoutResponse::class
        );
    }
}
