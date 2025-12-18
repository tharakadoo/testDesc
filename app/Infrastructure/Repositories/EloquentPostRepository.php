<?php

namespace App\Infrastructure\Repositories;


use App\Post\Entities\Post;
use App\Post\Repositories\PostRepositoryInterface;

final class EloquentPostRepository implements PostRepositoryInterface
{
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function findById(int|string $id): ?Post
    {
        return Post::find($id);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }
}
