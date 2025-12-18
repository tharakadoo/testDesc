<?php

namespace App\Post\Entities;

use App\User\Entities\User;
use App\Website\Entities\Website;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['website_id', 'title', 'description'];

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function emailedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_email_recipients', 'post_id', 'user_id');
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
