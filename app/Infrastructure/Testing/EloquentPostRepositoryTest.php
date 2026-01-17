<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\Post\Entities\Post;
use App\Website\Entities\Website;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\EloquentPostRepository;

class EloquentPostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentPostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentPostRepository();
    }

    #[Test]
    public function when_create_called_then_post_persisted_with_attributes(): void
    {
        $website = Website::factory()->create();
        $data = [
            'title' => 'Test Post Title',
            'description' => 'Test post description',
            'website_id' => $website->id,
        ];

        $post = $this->repository->create($data);

        $this->assertPostCreatedWithAttributes($post, $data);
    }

    #[Test]
    public function when_find_by_id_with_existing_post_then_returns_post(): void
    {
        $website = Website::factory()->create();
        $existingPost = Post::factory()->create(['website_id' => $website->id]);

        $foundPost = $this->repository->findById($existingPost->id);

        $this->assertPostFound($foundPost, $existingPost);
    }

    #[Test]
    public function when_find_by_id_with_non_existing_id_then_returns_null(): void
    {
        $result = $this->repository->findById(99999);

        $this->assertNull($result);
    }

    #[Test]
    public function when_update_called_then_post_attributes_changed(): void
    {
        $website = Website::factory()->create();
        $post = Post::factory()->create([
            'website_id' => $website->id,
            'title' => 'Original Title',
            'description' => 'Original description',
        ]);

        $newData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
        ];

        $updatedPost = $this->repository->update($post, $newData);

        $this->assertPostUpdatedWithAttributes($updatedPost, $newData);
    }

    #[Test]
    public function when_delete_called_then_post_removed_and_others_unchanged(): void
    {
        $website = Website::factory()->create();
        $postToDelete = Post::factory()->create(['website_id' => $website->id]);
        $postToKeep = Post::factory()->create(['website_id' => $website->id]);

        $result = $this->repository->delete($postToDelete);

        $this->assertPostDeletedAndOthersUnchanged($result, $postToDelete, $postToKeep);
    }

    protected function assertPostCreatedWithAttributes(Post $post, array $data): void
    {
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals($data['website_id'], $post->website_id);

        $freshPost = $post->fresh();
        $this->assertNotNull($freshPost, 'Post should be persisted in database');
    }

    protected function assertPostFound(?Post $foundPost, Post $existingPost): void
    {
        $this->assertNotNull($foundPost);
        $this->assertEquals($existingPost->id, $foundPost->id);
        $this->assertEquals($existingPost->title, $foundPost->title);
    }

    protected function assertPostUpdatedWithAttributes(Post $post, array $newData): void
    {
        $this->assertEquals($newData['title'], $post->title);
        $this->assertEquals($newData['description'], $post->description);

        $freshPost = $post->fresh();
        $this->assertEquals($newData['title'], $freshPost->title);
        $this->assertEquals($newData['description'], $freshPost->description);
    }

    protected function assertPostDeletedAndOthersUnchanged(bool $result, Post $deletedPost, Post $keptPost): void
    {
        $this->assertTrue($result);
        $this->assertNull(Post::find($deletedPost->id), 'Deleted post should not exist');
        $this->assertNotNull(Post::find($keptPost->id), 'Other post should still exist');
    }
}
