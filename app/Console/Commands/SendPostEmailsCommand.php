<?php

namespace App\Console\Commands;

use App\Post\Entities\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Post\Contracts\EmailServiceContract;

class SendPostEmailsCommand extends Command
{
    protected $signature = 'posts:send-emails {post_id : The ID of the post to send emails for}';

    protected $description = 'Send emails to all subscribers for a specific post';

    public function __construct(
        private EmailServiceContract $emailService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $postId = $this->argument('post_id');

        $post = Post::with('website')->find($postId);

        if (!$post) {
            $this->error("Post with ID {$postId} not found.");
            return Command::FAILURE;
        }

        $website = $post->website;

        if (!$website) {
            $this->error("Website not found for post ID {$postId}.");
            return Command::FAILURE;
        }

        $users = Cache::remember(
            "website_{$website->id}_users",
            60,
            fn() => $website->users()->get()
        );

        if ($users->isEmpty()) {
            $this->info("No subscribers found for website: {$website->url}");
            return Command::SUCCESS;
        }

        $sentCount = 0;

        foreach ($users as $user) {
            if (!$post->emailedUsers()->where('user_id', $user->id)->exists()) {
                $this->emailService->send([
                    'to' => $user->email,
                    'post' => $post,
                ]);

                $post->emailedUsers()->attach($user->id);
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} emails for post: {$post->title}");

        return Command::SUCCESS;
    }
}
