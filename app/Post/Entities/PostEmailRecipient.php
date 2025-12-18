<?php

namespace App\Post\Entities;

use App\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostEmailRecipient extends Model
{
    protected $table = 'post_email_recipient';
    protected $fillable = ['post_id', 'user_id'];

    public function post() : BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
