<?php

namespace App\Post\Testing;

use Mockery;
use Tests\TestCase;
use App\Post\Entities\Post;
use App\User\Entities\User;
use App\Application\Contracts\CacheContract;
use App\Post\UseCases\PostSubmitUseCase;
use App\Post\Contracts\EmailServiceContract;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Post\Repositories\PostRepositoryInterface;
use App\Website\Contracts\WebsiteUserServiceContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostSubmitUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('missingFieldProvider')]
    public function when_missing_required_field_then_throws_validation_exception(array $postData, string $field, array $expectedMessage): void
    {
        $useCase = $this->createUseCase();

        $this->assertValidationException($useCase, $postData, $field, $expectedMessage);
    }

    #[Test]
    public function when_post_created_then_post_saved(): void
    {
        $postData = $this->buildPostData();
        $post = $this->createMockPost($postData);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($postData)
            ->andReturn($post);

        $userProvider = Mockery::mock(WebsiteUserServiceContract::class);
        $userProvider->shouldReceive('getUsersForWebsite')
            ->andReturn(collect([]));

        $emailService = Mockery::mock(EmailServiceContract::class);

        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('remember')
            ->andReturnUsing(fn($key, $seconds, $callback) => $callback());

        $useCase = new PostSubmitUseCase($repository, $userProvider, $emailService, $cache);

        $result = $useCase->execute($postData);

        $this->assertPostCreated($result, $postData);
    }

    #[Test]
    public function when_post_created_then_emails_sent_to_subscribers(): void
    {
        $postData = $this->buildPostData();

        $user1 = new User(['id' => 1, 'email' => 'u1@example.com']);
        $user2 = new User(['id' => 2, 'email' => 'u2@example.com']);

        $post = $this->createMockPostWithEmailTracking($postData, [$user1, $user2]);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($postData)
            ->andReturn($post);

        $userProvider = Mockery::mock(WebsiteUserServiceContract::class);
        $userProvider->shouldReceive('getUsersForWebsite')
            ->once()
            ->with(1)
            ->andReturn(collect([$user1, $user2]));

        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldReceive('send')
            ->once()
            ->with(Mockery::subset(['to' => 'u1@example.com', 'post' => $post]));
        $emailService->shouldReceive('send')
            ->once()
            ->with(Mockery::subset(['to' => 'u2@example.com', 'post' => $post]));

        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('remember')
            ->andReturnUsing(fn($key, $seconds, $callback) => $callback());

        $useCase = new PostSubmitUseCase($repository, $userProvider, $emailService, $cache);

        $result = $useCase->execute($postData);

        $this->assertPostCreated($result, $postData);
    }

    #[Test]
    public function when_user_already_received_email_then_email_not_sent_again(): void
    {
        $postData = $this->buildPostData();

        $user1 = new User(['id' => 1, 'email' => 'u1@example.com']);
        $user2 = new User(['id' => 2, 'email' => 'u2@example.com']);

        $post = $this->createMockPostWithExistingEmailedUser($postData, $user1, $user2);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($postData)
            ->andReturn($post);

        $userProvider = Mockery::mock(WebsiteUserServiceContract::class);
        $userProvider->shouldReceive('getUsersForWebsite')
            ->once()
            ->with(1)
            ->andReturn(collect([$user1, $user2]));

        $emailService = Mockery::mock(EmailServiceContract::class);
        $emailService->shouldReceive('send')
            ->once()
            ->with(Mockery::subset(['to' => 'u2@example.com', 'post' => $post]));
        $emailService->shouldNotReceive('send')
            ->with(Mockery::subset(['to' => 'u1@example.com']));

        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('remember')
            ->andReturnUsing(fn($key, $seconds, $callback) => $callback());

        $useCase = new PostSubmitUseCase($repository, $userProvider, $emailService, $cache);

        $result = $useCase->execute($postData);

        $this->assertPostCreated($result, $postData);
    }

    public static function missingFieldProvider(): array
    {
        return [
            'missing title' => [
                ['title' => '', 'description' => 'desc', 'website_id' => 1],
                'title',
                ['Title is required'],
            ],
            'missing description' => [
                ['title' => 'title', 'description' => '', 'website_id' => 1],
                'description',
                ['Description is required'],
            ],
            'missing website_id' => [
                ['title' => 'title', 'description' => 'desc', 'website_id' => null],
                'website_id',
                ['Website is required'],
            ],
        ];
    }

    protected function createUseCase(): PostSubmitUseCase
    {
        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldNotReceive('create');

        $userProvider = Mockery::mock(WebsiteUserServiceContract::class);
        $emailService = Mockery::mock(EmailServiceContract::class);
        $cache = Mockery::mock(CacheContract::class);

        return new PostSubmitUseCase($repository, $userProvider, $emailService, $cache);
    }

    protected function buildPostData(): array
    {
        return [
            'title' => 'Sample Title',
            'description' => 'Sample description',
            'website_id' => 1,
        ];
    }

    protected function createMockPost(array $postData): Post
    {
        $post = Mockery::mock(Post::class)->makePartial();
        $post->shouldReceive('getAttribute')->with('title')->andReturn($postData['title']);
        $post->shouldReceive('getAttribute')->with('description')->andReturn($postData['description']);
        $post->shouldReceive('getAttribute')->with('website_id')->andReturn($postData['website_id']);

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('where')->andReturnSelf();
        $relation->shouldReceive('exists')->andReturn(false);
        $relation->shouldReceive('attach');
        $post->shouldReceive('emailedUsers')->andReturn($relation);

        return $post;
    }

    protected function createMockPostWithEmailTracking(array $postData, array $users): Post
    {
        $post = Mockery::mock(Post::class)->makePartial();
        $post->shouldReceive('getAttribute')->with('title')->andReturn($postData['title']);
        $post->shouldReceive('getAttribute')->with('description')->andReturn($postData['description']);
        $post->shouldReceive('getAttribute')->with('website_id')->andReturn($postData['website_id']);

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('where')->andReturnSelf();
        $relation->shouldReceive('exists')->andReturn(false);

        foreach ($users as $user) {
            $relation->shouldReceive('attach')->with($user->id)->once();
        }

        $post->shouldReceive('emailedUsers')->andReturn($relation);

        return $post;
    }

    protected function createMockPostWithExistingEmailedUser(array $postData, User $existingUser, User $newUser): Post
    {
        $post = Mockery::mock(Post::class)->makePartial();
        $post->shouldReceive('getAttribute')->with('title')->andReturn($postData['title']);
        $post->shouldReceive('getAttribute')->with('description')->andReturn($postData['description']);
        $post->shouldReceive('getAttribute')->with('website_id')->andReturn($postData['website_id']);

        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('where')->with('user_id', $existingUser->id)->andReturnSelf();
        $relation->shouldReceive('where')->with('user_id', $newUser->id)->andReturnSelf();
        $relation->shouldReceive('exists')->andReturn(true, false);
        $relation->shouldReceive('attach')->with($newUser->id)->once();

        $post->shouldReceive('emailedUsers')->andReturn($relation);

        return $post;
    }

    protected function assertValidationException(PostSubmitUseCase $useCase, array $postData, string $field, array $expectedMessage): void
    {
        try {
            $useCase->execute($postData);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($field, $e->errors());
            $this->assertEquals($expectedMessage, $e->errors()[$field]);
        }
    }

    protected function assertPostCreated(Post $created, array $postData): void
    {
        $this->assertInstanceOf(Post::class, $created);
        $this->assertEquals($postData['title'], $created->title);
        $this->assertEquals($postData['description'], $created->description);
        $this->assertEquals($postData['website_id'], $created->website_id);
    }
}
