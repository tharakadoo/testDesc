<?php

namespace App\Console\Commands;

use App\Post\Entities\Post;
use App\Post\Events\PostPublished;
use Illuminate\Console\Command;

class SendPostEmailsCommand extends Command
{
    protected $signature = 'posts:send-emails {post_id : The ID of the post to send emails for}';

    protected $description = 'Send emails to all subscribers for a specific post';

    public function handle(): int
    {
        $postId = $this->argument('post_id');

        $post = Post::with('website')->find($postId);

        if (!$post) {
            $this->error("Post with ID {$postId} not found.");
            return Command::FAILURE;
        }

        if (!$post->website) {
            $this->error("Website not found for post ID {$postId}.");
            return Command::FAILURE;
        }

        PostPublished::dispatch($post);

        $this->info("Email job dispatched for post: {$post->title}");

        return Command::SUCCESS;
    }
}
