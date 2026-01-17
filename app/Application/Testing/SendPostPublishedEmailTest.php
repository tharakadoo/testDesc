<?php

namespace App\Application\Testing;

use Mockery;
use Tests\TestCase;
use App\Post\Entities\Post;
use App\User\Entities\User;
use App\Website\Entities\Website;
use App\Post\Events\PostPublished;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use App\Post\Contracts\EmailServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Application\Listeners\SendPostPublishedEmail;

class SendPostPublishedEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function when_website_is_null_then_no_email_sent(): void
    {
        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldNotReceive('send');

        $post = Mockery::mock(Post::class)->makePartial();
        $post->shouldReceive('loadMissing')->with('website')->andReturnSelf();
        $post->shouldReceive('getAttribute')->with('website')->andReturnNull();

        $event = new PostPublished($post);
        $listener = new SendPostPublishedEmail($emailService);

        $listener->handle($event);

        $this->assertNoEmailSent($emailService);
    }

    #[Test]
    public function when_user_already_received_email_then_user_skipped(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user = User::factory()->create();

        $website->users()->attach($user->id);
        $post->markEmailSentTo($user);

        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldNotReceive('send');

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$user]));

        $event = new PostPublished($post);
        $listener = new SendPostPublishedEmail($emailService);

        $listener->handle($event);

        $this->assertNoEmailSent($emailService);
    }

    #[Test]
    public function when_user_has_not_received_email_then_email_sent_and_marked(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);
        $user = User::factory()->create();

        $website->users()->attach($user->id);

        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($data) use ($user, $post) {
                return $data['to'] === $user->email && $data['post']->id === $post->id;
            }));

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$user]));

        $event = new PostPublished($post);
        $listener = new SendPostPublishedEmail($emailService);

        $listener->handle($event);

        $this->assertEmailMarkedAsSent($post, $user);
    }

    #[Test]
    public function when_multiple_users_subscribed_then_only_eligible_users_receive_email(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create(['website_id' => $website->id]);

        $userAlreadyEmailed = User::factory()->create();
        $userEligible1 = User::factory()->create();
        $userEligible2 = User::factory()->create();

        $website->users()->attach([$userAlreadyEmailed->id, $userEligible1->id, $userEligible2->id]);
        $post->markEmailSentTo($userAlreadyEmailed);

        $emailsSent = [];
        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldReceive('send')
            ->twice()
            ->with(Mockery::on(function ($data) use (&$emailsSent) {
                $emailsSent[] = $data['to'];
                return true;
            }));

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$userAlreadyEmailed, $userEligible1, $userEligible2]));

        $event = new PostPublished($post);
        $listener = new SendPostPublishedEmail($emailService);

        $listener->handle($event);

        $this->assertEmailSentToUsers($emailsSent, [$userEligible1, $userEligible2]);
        $this->assertEmailNotSentToUser($emailsSent, $userAlreadyEmailed);
        $this->assertEmailMarkedAsSent($post, $userEligible1);
        $this->assertEmailMarkedAsSent($post, $userEligible2);
    }

    protected function assertNoEmailSent(EmailServiceContract $emailService): void
    {
        // Mockery will fail if send() was called when shouldNotReceive was set
        $this->assertTrue(true);
    }

    protected function assertEmailMarkedAsSent(Post $post, User $user): void
    {
        $this->assertTrue(
            $post->fresh()->hasUserReceivedEmail($user),
            "Expected email to be marked as sent to user {$user->email}"
        );
    }

    protected function assertEmailSentToUsers(array $emailsSent, array $users): void
    {
        foreach ($users as $user) {
            $this->assertContains(
                $user->email,
                $emailsSent,
                "Expected email to be sent to {$user->email}"
            );
        }
    }

    protected function assertEmailNotSentToUser(array $emailsSent, User $user): void
    {
        $this->assertNotContains(
            $user->email,
            $emailsSent,
            "Expected email NOT to be sent to {$user->email}"
        );
    }
}
