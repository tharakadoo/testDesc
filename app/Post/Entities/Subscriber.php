<?php

namespace App\Post\Entities;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['website_id', 'user_id'];

}
