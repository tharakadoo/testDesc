<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\User\Entities\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\EloquentUserRepository;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
    }

    #[Test]
    public function when_user_exists_then_find_or_create_returns_existing_user(): void
    {
        $existingUser = User::factory()->create(['email' => 'john@example.com']);

        $foundUser = $this->repository->findOrCreate('john@example.com');

        $this->assertUserFoundCorrectly($foundUser, $existingUser);
    }

    #[Test]
    public function when_user_does_not_exist_then_find_or_create_creates_new_user(): void
    {
        $email = 'new@example.com';
        $this->assertDatabaseMissing('users', ['email' => $email]);

        $createdUser = $this->repository->findOrCreate($email);

        $this->assertUserCreatedWithCorrectAttributes($createdUser, $email);
    }

    #[Test]
    public function when_find_or_create_called_multiple_times_then_returns_same_user(): void
    {
        $email = 'alice@example.com';
        $user1 = $this->repository->findOrCreate($email);
        $user2 = $this->repository->findOrCreate($email);

        $this->assertEquals($user1->id, $user2->id, 'Should return the same user');
        $this->assertSingleUserInDatabase($email);
    }

    #[Test]
    public function when_creating_user_then_name_extracted_from_email(): void
    {
        $email = 'bob@example.com';
        $createdUser = $this->repository->findOrCreate($email);

        $this->assertEquals('bob', $createdUser->name);
    }

    protected function assertUserFoundCorrectly(User $foundUser, User $existingUser): void
    {
        $this->assertNotNull($foundUser);
        $this->assertEquals($existingUser->id, $foundUser->id);
        $this->assertEquals($existingUser->email, $foundUser->email);
    }

    protected function assertUserCreatedWithCorrectAttributes(User $user, string $email): void
    {
        $this->assertNotNull($user->id);
        $this->assertEquals($email, $user->email);
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    protected function assertSingleUserInDatabase(string $email): void
    {
        $count = User::where('email', $email)->count();
        $this->assertEquals(1, $count, "Expected exactly 1 user with email {$email}");
    }

    protected function assertEqual($expected, $actual, string $message = ''): void
    {
        $this->assertEquals($expected, $actual, $message);
    }
}
