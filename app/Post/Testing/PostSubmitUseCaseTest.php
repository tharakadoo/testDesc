<?php

namespace App\Post\Testing;

use Mockery;
use Tests\TestCase;
use App\Post\Entities\Post;
use App\Post\Events\PostPublished;
use App\Application\Contracts\TransactionContract;
use App\Post\DataTransferObjects\PostResult;
use App\Post\UseCases\PostSubmitUseCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Post\Repositories\PostRepositoryInterface;

class PostSubmitUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('missingFieldProvider')]
    public function when_missing_required_field_then_throws_validation_exception(array $submitPost, string $field, array $expectedMessage): void
    {
        $useCase = $this->createUseCase();

        $this->assertValidationException($useCase, $submitPost, $field, $expectedMessage);
    }

    #[Test]
    public function when_post_created_then_post_saved(): void
    {
        Event::fake();

        $submitPost = $this->buildSubmitPost();
        $post = $this->createMockPost($submitPost);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($submitPost)
            ->andReturn($post);

        $transaction = $this->createMockTransaction();

        $useCase = new PostSubmitUseCase($repository, $transaction);

        $result = $useCase->execute($submitPost);

        $this->assertPostCreated($result, $submitPost);
    }

    #[Test]
    public function when_post_created_then_post_published_event_dispatched(): void
    {
        Event::fake();

        $submitPost = $this->buildSubmitPost();
        $post = $this->createMockPost($submitPost);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($submitPost)
            ->andReturn($post);

        $transaction = $this->createMockTransaction();

        $useCase = new PostSubmitUseCase($repository, $transaction);

        $useCase->execute($submitPost);

        Event::assertDispatched(PostPublished::class, function ($event) use ($post) {
            return $event->post === $post;
        });
    }

    protected function createUseCase(): PostSubmitUseCase
    {
        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldNotReceive('create');

        $transaction = $this->createMockTransaction();

        return new PostSubmitUseCase($repository, $transaction);
    }

    protected function createMockTransaction(): TransactionContract
    {
        $transaction = Mockery::mock(TransactionContract::class);
        $transaction->shouldReceive('execute')
            ->andReturnUsing(fn(callable $callback) => $callback());

        return $transaction;
    }

    protected function assertValidationException(PostSubmitUseCase $useCase, array $submitPost, string $field, array $expectedMessage): void
    {
        try {
            $useCase->execute($submitPost);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($field, $e->errors());
            $this->assertEquals($expectedMessage, $e->errors()[$field]);
        }
    }

    protected function buildSubmitPost(): array
    {
        return [
            'title' => 'Sample Title',
            'description' => 'Sample description',
            'website_id' => 1,
        ];
    }

    protected function createMockPost(array $submitPost): Post
    {
        $post = Mockery::mock(Post::class)->makePartial();
        $post->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $post->shouldReceive('getAttribute')->with('title')->andReturn($submitPost['title']);
        $post->shouldReceive('getAttribute')->with('description')->andReturn($submitPost['description']);
        $post->shouldReceive('getAttribute')->with('website_id')->andReturn($submitPost['website_id']);

        return $post;
    }

    protected function assertPostCreated(PostResult $result, array $submitPost): void
    {
        $this->assertInstanceOf(PostResult::class, $result);
        $this->assertEquals($submitPost['title'], $result->title);
        $this->assertEquals($submitPost['description'], $result->description);
        $this->assertEquals($submitPost['website_id'], $result->website_id);
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
}
