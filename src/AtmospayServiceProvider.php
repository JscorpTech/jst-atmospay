<?php

namespace JscorpTech\Atmospay;

use Illuminate\Support\ServiceProvider;

class AtmospayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__."/../database/migrations");
        $this->loadRoutesFrom(__DIR__."/../routes/api.php");
    }
}
