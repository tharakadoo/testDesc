<?php

namespace App\Providers;

use App\Infrastructure\Repositories\EloquentSubscriptionRepository;
use App\Infrastructure\Repositories\EloquentWebsiteRepository;
use App\Infrastructure\Services\EloquentWebsiteUserService;
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
            WebsiteUserServiceContract::class,
            EloquentWebsiteUserService::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            EloquentSubscriptionRepository::class
        );

        $this->app->bind(
            WebsiteRepositoryInterface::class,
            EloquentWebsiteRepository::class
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
