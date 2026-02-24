<?php

namespace App\User\UseCases\Repositories;

use App\User\Entities\User;

interface UserRepositoryInterface
{
    public function findOrCreate(string $email): User;
}
