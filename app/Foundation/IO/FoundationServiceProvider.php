<?php

namespace App\Foundation\IO;

use App\Foundation\UseCases\CacheContract;
use App\Foundation\UseCases\TransactionContract;
use App\Foundation\IO\ExternalServices\LaravelCacheService;
use App\Foundation\IO\ExternalServices\LaravelTransactionService;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CacheContract::class,
            LaravelCacheService::class
        );

        $this->app->bind(
            TransactionContract::class,
            LaravelTransactionService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }
}
