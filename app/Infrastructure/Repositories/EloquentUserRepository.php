<?php

namespace App\Infrastructure\Repositories;

use App\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use App\User\Repositories\UserRepositoryInterface;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findOrCreate(string $email): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => explode('@', $email)[0],
                'password' => Hash::make('temporary-password'),
            ]
        );
    }
}
