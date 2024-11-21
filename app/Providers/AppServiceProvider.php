<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NoiseLevelBufferService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NoiseLevelBufferService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
