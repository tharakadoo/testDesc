<?php

namespace App\Website\Entities;

use App\Post\Entities\Post;
use App\User\Entities\User;
use Database\Factories\WebsiteFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Website extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    protected static function newFactory(): WebsiteFactory
    {
        return WebsiteFactory::new();
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscribers', 'website_id', 'user_id');
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

}
