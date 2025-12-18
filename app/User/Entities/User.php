<?php

namespace App\User\Entities;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'name'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

}
