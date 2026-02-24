<?php

namespace App\User\Entities;

use App\User\IO\Database\factories\UserFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'name', 'password'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

}
