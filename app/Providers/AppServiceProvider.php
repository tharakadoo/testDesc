<?php

namespace App\Providers;

use App\Application\Contracts\EmailServiceContract;
use App\Infrastructure\Repositories\EloquentPostRepository;
use App\Infrastructure\Services\EloquentWebsiteUserService;
use App\Infrastructure\Services\LaravelEmailService;
use App\Post\Repositories\PostRepositoryInterface;
use App\Website\Contracts\WebsiteUserServiceContract;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
