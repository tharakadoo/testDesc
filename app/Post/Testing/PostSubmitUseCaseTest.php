<?php

namespace App\Post\Testing;

use App\Application\Contracts\EmailServiceContract;
use App\User\Entities\User;
use App\Website\Contracts\WebsiteUserServiceContract;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;
use App\Post\Entities\Post;
use App\Website\Entities\Website;
use App\Post\UseCases\PostSubmitUseCase;
use Illuminate\Validation\ValidationException;
use App\Post\Repositories\PostRepositoryInterface;

class PostSubmitUseCaseTest extends TestCase
{
    protected Website $website;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_when_create_post_then_it_created(): void
    {
        $postData = [
            'title' => 'Sample Title',
            'description' => 'Sample description',
            'website_id' => 1,
        ];

        $expectedPost = new Post($postData);

        $repository = Mockery::mock(PostRepositoryInterface::class);

        $repository->shouldReceive('create')
            ->once()
            ->with($postData)
            ->andReturn($expectedPost);

        $useCase = new PostSubmitUseCase($repository);

        $created = $useCase->execute($postData);

        $this->assertPostCreated($created, $postData);
    }

    /**
     * @dataProvider missingFieldProvider
     */
    public function test_when_missing_required_field_then_throws_validation_exception(array $postData, string $field, array $expectedMessage)
    {
        $mock = Mockery::mock(PostRepositoryInterface::class);
        $mock->shouldNotReceive('create');

        $useCase = new PostSubmitUseCase($mock);

        try {
            $useCase->execute($postData);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($field, $e->errors());
            $this->assertEquals($expectedMessage, $e->errors()[$field]);
        }
    }

    public function test_when_post_created_then_emails_are_sent_to_website_users(): void
    {
        $postData = [
            'title' => 'Sample Title',
            'description' => 'Sample description',
            'website_id' => 1,
        ];

        $post = new Post($postData);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($postData)
            ->andReturn($post);

        $userProvider = Mockery::mock(WebsiteUserServiceContract::class);

        $user1 = new User(['email' => 'u1@example.com']);
        $user2 = new User(['email' => 'u2@example.com']);

        $userProvider->shouldReceive('getUsersForWebsite')
            ->once()
            ->with(1)
            ->andReturn(collect([$user1, $user2]));

        $emailService = Mockery::mock(EmailServiceContract::class);
        foreach ([$user1, $user2] as $user) {
            $emailService->shouldReceive('send')
                ->once()
                ->with(Mockery::subset(['to' => $user->email, 'post' => $post]));
        }

        $useCase = new PostSubmitUseCase(
            $repository,
            $userProvider,
            $emailService
        );

        $useCase->execute($postData);
    }

    public static function missingFieldProvider(): array
    {
        return [
            'missing title' => [
                ['title' => '', 'description' => 'desc', 'website_id' => 1],
                'title',
                ['Title is required']
            ],
            'missing description' => [
                ['title' => 'title', 'description' => '', 'website_id' => 1],
                'description',
                ['Description is required']
            ],
            'missing website_id' => [
                ['title' => 'title', 'description' => 'desc', 'website_id' => null],
                'website_id',
                ['Website is required']
            ],
        ];
    }

    protected function assertPostCreated(Post $created, array $postData): void
    {
        $this->assertInstanceOf(Post::class, $created);
        $this->assertEquals($postData['title'], $created->title);
        $this->assertEquals($postData['description'], $created->description);
        $this->assertEquals($postData['website_id'], $created->website_id);
    }

}
