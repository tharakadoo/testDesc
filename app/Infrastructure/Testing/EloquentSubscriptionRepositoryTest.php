<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\User\Entities\User;
use App\Website\Entities\Website;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\EloquentSubscriptionRepository;

class EloquentSubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentSubscriptionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentSubscriptionRepository();
    }

    #[Test]
    public function when_user_subscribed_to_website_then_is_subscribed_returns_true(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create();
        $otherWebsite = Website::factory()->create();

        $website->users()->attach($user->id);

        $this->assertUserSubscribedToWebsite($user, $website);
        $this->assertUserNotSubscribedToWebsite($user, $otherWebsite);
    }

    #[Test]
    public function when_user_not_subscribed_to_website_then_is_subscribed_returns_false(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create();

        $this->assertUserNotSubscribedToWebsite($user, $website);
    }

    #[Test]
    public function when_subscribe_called_then_user_attached_to_website(): void
    {
        $user = User::factory()->create();
        $website = Website::factory()->create();
        $otherWebsite = Website::factory()->create();

        $this->repository->subscribe($user, $website);

        $this->assertUserSubscribedToWebsite($user, $website);
        $this->assertUserNotSubscribedToWebsite($user, $otherWebsite);
    }

    protected function assertUserSubscribedToWebsite(User $user, Website $website): void
    {
        $this->assertTrue(
            $this->repository->isSubscribed($user, $website),
            "Expected user {$user->email} to be subscribed to website {$website->url}"
        );
    }

    protected function assertUserNotSubscribedToWebsite(User $user, Website $website): void
    {
        $this->assertFalse(
            $this->repository->isSubscribed($user, $website),
            "Expected user {$user->email} NOT to be subscribed to website {$website->url}"
        );
    }
}
