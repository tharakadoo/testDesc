<?php

namespace App\Providers;

use App\Application\Listeners\SendPostPublishedEmail;
use App\Post\Events\PostPublished;

class EventServiceProvider
{
    protected array $listen = [
        PostPublished::class => [
            SendPostPublishedEmail::class,
        ],
    ];
}
