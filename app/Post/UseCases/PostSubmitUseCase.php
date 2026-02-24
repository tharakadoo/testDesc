<?php

namespace App\Post\UseCases;

use App\Post\Entities\Events\PostPublished;
use App\Foundation\UseCases\TransactionContract;
use App\Post\UseCases\DataTransferObjects\PostResult;
use App\Post\UseCases\DataTransferObjects\SubmitPostData;
use App\Post\UseCases\Repositories\PostRepositoryInterface;

class PostSubmitUseCase
{
    public function __construct(
        private PostRepositoryInterface $post,
        private TransactionContract $transaction,
    ) {}

    public function execute(array $submitPost): PostResult
    {
        $dto = SubmitPostData::fromArray($submitPost);

        return $this->transaction->execute(function () use ($dto) {
            $post = $this->post->create([
                'title' => $dto->title,
                'description' => $dto->description,
                'website_id' => $dto->website_id,
            ]);

            PostPublished::dispatch($post);

            return new PostResult(
                id: $post->id,
                title: $post->title,
                description: $post->description,
                website_id: $post->website_id,
            );
        });
    }
}
