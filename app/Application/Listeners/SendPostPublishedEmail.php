<?php

namespace App\Application\Listeners;

use App\Post\Contracts\EmailServiceContract;
use App\Post\Events\PostPublished;
use Illuminate\Support\Facades\Cache;

class SendPostPublishedEmail
{
    protected EmailServiceContract $emailService;

    public function __construct(EmailServiceContract $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(PostPublished $event): void
    {
        $post = $event->post;
        $post->loadMissing('website');

        $website = $event->post->website;

        if (!$website) {
            return;
        }

        $users = Cache::remember(
            "website_{$website->id}_users",
            60,
            fn() => $website->users()->get()
        );

        foreach ($users as $user) {
            if (!$post->emailedUsers()->where('user_id', $user->id)->exists()) {
                $this->emailService->send([
                    'to'   => $user->email,
                    'post' => $post,
                ]);

                $post->emailedUsers()->attach($user->id);
            }
        }
    }

}
