<?php

namespace App\User\IO\Database;

use App\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use App\User\UseCases\Repositories\UserRepositoryInterface;

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
