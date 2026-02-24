<?php

namespace App\Website\IO;

use App\Website\IO\Database\EloquentSubscriptionRepository;
use App\Website\IO\Database\EloquentWebsiteRepository;
use App\Website\IO\Database\EloquentWebsiteUserService;
use App\Website\UseCases\Repositories\SubscriptionRepositoryInterface;
use App\Website\UseCases\Repositories\WebsiteRepositoryInterface;
use App\Website\UseCases\WebsiteUserServiceContract;
use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            WebsiteRepositoryInterface::class,
            EloquentWebsiteRepository::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            EloquentSubscriptionRepository::class
        );

        $this->app->bind(
            WebsiteUserServiceContract::class,
            EloquentWebsiteUserService::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }
}
