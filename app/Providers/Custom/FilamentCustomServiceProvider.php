<?php

namespace App\Providers\Custom;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class FilamentCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override the pagination component to avoid using Number::format
        $this->loadViewsFrom(resource_path('views/vendor/filament'), 'filament');
    }
}