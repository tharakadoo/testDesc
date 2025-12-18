<?php

namespace App\Post\UseCases;

use App\Post\Entities\Post;
use App\Post\DataTransferObjects\SubmitPostData;
use App\Post\Events\PostPublished;
use App\Post\Repositories\PostRepositoryInterface;

class PostSubmitUseCase
{
    public function __construct(
        private PostRepositoryInterface $post,
    ) {}

    public function execute(array $data): Post
    {
        $dto = SubmitPostData::fromArray($data);

        $post = $this->post->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'website_id' => $dto->website_id
        ]);

        event(new PostPublished($post));

        return $post;
    }
}
