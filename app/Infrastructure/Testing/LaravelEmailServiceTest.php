<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\Post\Entities\Post;
use App\Mail\PostPublishedMail;
use App\Website\Entities\Website;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Services\LaravelEmailService;

class LaravelEmailServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function when_send_called_then_mail_queued_to_recipient(): void
    {
        Mail::fake();

        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $recipientEmail = 'test@example.com';

        $service = new LaravelEmailService();
        $service->send([
            'to' => $recipientEmail,
            'post' => $post,
        ]);

        $this->assertMailQueuedToRecipient($recipientEmail, $post);
    }

    protected function assertMailQueuedToRecipient(string $email, Post $post): void
    {
        Mail::assertQueued(PostPublishedMail::class, function ($mail) use ($email, $post) {
            return $mail->hasTo($email) && $mail->post->id === $post->id;
        });
    }
}
