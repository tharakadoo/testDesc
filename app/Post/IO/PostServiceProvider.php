<?php

namespace App\Post\IO;

use App\Post\Entities\Events\PostPublished;
use App\Post\IO\Database\EloquentPostRepository;
use App\Post\IO\ExternalServices\LaravelEmailService;
use App\Post\IO\Listeners\SendPostPublishedEmail;
use App\Post\UseCases\EmailServiceContract;
use App\Post\UseCases\Repositories\PostRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PostRepositoryInterface::class,
            EloquentPostRepository::class
        );

        $this->app->bind(
            EmailServiceContract::class,
            LaravelEmailService::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        Event::listen(PostPublished::class, SendPostPublishedEmail::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Http\Commands\SendPostEmailsCommand::class,
            ]);
        }
    }
}
