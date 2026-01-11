<?php

namespace App\Providers;

use App\Application\Contracts\CacheContract;
use App\Post\Contracts\EmailServiceContract;
use App\Infrastructure\Repositories\EloquentPostRepository;
use App\Infrastructure\Repositories\EloquentSubscriptionRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Infrastructure\Repositories\EloquentWebsiteRepository;
use App\Infrastructure\Services\EloquentWebsiteUserService;
use App\Infrastructure\Services\LaravelCacheService;
use App\Infrastructure\Services\LaravelEmailService;
use App\Post\Repositories\PostRepositoryInterface;
use App\User\Repositories\UserRepositoryInterface;
use App\Website\Contracts\WebsiteUserServiceContract;
use App\Website\Repositories\SubscriptionRepositoryInterface;
use App\Website\Repositories\WebsiteRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PostRepositoryInterface::class,
            EloquentPostRepository::class
        );

        // Bind application contracts
        $this->app->bind(
            WebsiteUserServiceContract::class,
            EloquentWebsiteUserService::class
        );

        $this->app->bind(
            EmailServiceContract::class,
            LaravelEmailService::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            EloquentSubscriptionRepository::class
        );

        $this->app->bind(
            CacheContract::class,
            LaravelCacheService::class
        );

        $this->app->bind(
            WebsiteRepositoryInterface::class,
            EloquentWebsiteRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
