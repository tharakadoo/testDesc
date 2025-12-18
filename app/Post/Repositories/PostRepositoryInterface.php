<?php

namespace App\Post\Repositories;

use App\Post\Entities\Post;

interface PostRepositoryInterface
{
    public function create(array $data): Post;

    public function findById(int|string $id): ?Post;

    public function update(Post $post, array $data): Post;

    public function delete(Post $post): bool;
}
