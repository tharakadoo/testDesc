<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\User\Entities\User;
use App\Website\Entities\Website;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Services\EloquentWebsiteUserService;

class EloquentWebsiteUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private EloquentWebsiteUserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EloquentWebsiteUserService();
    }

    #[Test]
    public function when_get_users_for_website_then_returns_subscribed_users(): void
    {
        $website = Website::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $website->users()->attach($user1->id);
        $website->users()->attach($user2->id);

        $users = $this->service->getUsersForWebsite($website->id);

        $this->assertUsersReturnedCorrectly($users, [$user1, $user2]);
        $this->assertUserNotIncluded($users, $user3);
    }

    #[Test]
    public function when_website_has_no_users_then_returns_empty_collection(): void
    {
        $website = Website::factory()->create();

        $users = $this->service->getUsersForWebsite($website->id);

        $this->assertTrue($users->isEmpty());
    }

    #[Test]
    public function when_multiple_websites_exist_then_returns_only_specified_website_users(): void
    {
        $website1 = Website::factory()->create();
        $website2 = Website::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $website1->users()->attach([$user1->id, $user2->id]);
        $website2->users()->attach($user3->id);

        $website1Users = $this->service->getUsersForWebsite($website1->id);

        $this->assertCount(2, $website1Users);
        $this->assertTrue($website1Users->contains('id', $user1->id));
        $this->assertTrue($website1Users->contains('id', $user2->id));
        $this->assertFalse($website1Users->contains('id', $user3->id));
    }

    #[Test]
    public function when_get_users_for_non_existing_website_then_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->service->getUsersForWebsite(99999);
    }

    protected function assertUsersReturnedCorrectly($collection, array $expectedUsers): void
    {
        $this->assertCount(count($expectedUsers), $collection);

        foreach ($expectedUsers as $user) {
            $this->assertTrue(
                $collection->contains('id', $user->id),
                "Expected user {$user->email} in collection"
            );
        }
    }

    protected function assertUserNotIncluded($collection, User $user): void
    {
        $this->assertFalse(
            $collection->contains('id', $user->id),
            "User {$user->email} should not be in collection"
        );
    }
}
