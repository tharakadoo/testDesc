<?php

namespace App\Console\Testing;

use Tests\TestCase;
use App\Post\Entities\Post;
use Illuminate\Console\Command;
use App\Website\Entities\Website;
use App\Post\Events\PostPublished;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendPostEmailsCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function when_post_not_found_then_returns_failure_with_error_message(): void
    {
        $this->artisan('posts:send-emails', ['post_id' => 99999])
            ->expectsOutput('Post with ID 99999 not found.')
            ->assertExitCode(Command::FAILURE);
    }

    #[Test]
    public function when_valid_post_provided_then_dispatches_event_and_returns_success(): void
    {
        Event::fake();

        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);

        $this->artisan('posts:send-emails', ['post_id' => $post->id])
            ->expectsOutput("Email job dispatched for post: {$post->title}")
            ->assertExitCode(Command::SUCCESS);

        $this->assertPostPublishedEventDispatched($post);
    }

    protected function assertPostPublishedEventDispatched(Post $post): void
    {
        Event::assertDispatched(PostPublished::class, function ($event) use ($post) {
            return $event->post->id === $post->id;
        });
    }
}
