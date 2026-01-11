<?php

namespace App\User\Repositories;

use App\User\Entities\User;

interface UserRepositoryInterface
{
    public function findOrCreate(string $email): User;
}
