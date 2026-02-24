<?php

namespace App\User\IO;

use App\User\IO\Database\EloquentUserRepository;
use App\User\UseCases\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }
}
