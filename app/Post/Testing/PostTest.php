<?php

namespace App\Post\Testing;

use Tests\TestCase;
use App\Post\Entities\Post;
use App\User\Entities\User;
use App\Website\Entities\Website;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function when_user_has_not_received_email_then_returns_false(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user = User::factory()->create();

        $this->assertFalse($post->hasUserReceivedEmail($user));
    }

    #[Test]
    public function when_user_has_received_email_then_returns_true(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user = User::factory()->create();

        $post->markEmailSentTo($user);

        $this->assertTrue($post->hasUserReceivedEmail($user));
    }

    #[Test]
    public function when_mark_email_sent_then_user_attached_to_post(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user = User::factory()->create();

        $post->markEmailSentTo($user);

        $this->assertDatabaseHas('post_email_recipients', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function when_multiple_users_marked_then_all_tracked(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $post->markEmailSentTo($user1);
        $post->markEmailSentTo($user2);

        $this->assertTrue($post->hasUserReceivedEmail($user1));
        $this->assertTrue($post->hasUserReceivedEmail($user2));
        $this->assertEquals(2, $post->emailedUsers()->count());
    }
}
