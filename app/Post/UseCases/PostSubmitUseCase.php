<?php

namespace App\Post\UseCases;

use App\Post\Entities\Post;
use App\User\Entities\User;
use App\Application\Contracts\CacheContract;
use App\Post\Contracts\EmailServiceContract;
use App\Post\DataTransferObjects\SubmitPostData;
use App\Post\Repositories\PostRepositoryInterface;
use App\Website\Contracts\WebsiteUserServiceContract;

class PostSubmitUseCase
{
    public function __construct(
        private PostRepositoryInterface $post,
        private WebsiteUserServiceContract $userProvider,
        private EmailServiceContract $emailService,
        private CacheContract $cache,
    ) {}

    public function execute(array $data): Post
    {
        $dto = SubmitPostData::fromArray($data);

        $post = $this->post->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'website_id' => $dto->website_id,
        ]);

        $this->sendEmailsToSubscribers($post);

        return $post;
    }

    private function sendEmailsToSubscribers(Post $post): void
    {
        $users = $this->getSubscribers($post->website_id);

        foreach ($users as $user) {
            if ($this->hasAlreadyReceivedEmail($post, $user)) {
                continue;
            }

            $this->emailService->send([
                'to' => $user->email,
                'post' => $post,
            ]);

            $this->markEmailSent($post, $user);
        }
    }

    private function getSubscribers(int $websiteId): \Illuminate\Support\Collection
    {
        return $this->cache->remember(
            "website_{$websiteId}_subscribers",
            60,
            fn () => $this->userProvider->getUsersForWebsite($websiteId)
        );
    }

    private function hasAlreadyReceivedEmail(Post $post, User $user): bool
    {
        return $post->emailedUsers()->where('user_id', $user->id)->exists();
    }

    private function markEmailSent(Post $post, User $user): void
    {
        $post->emailedUsers()->attach($user->id);
    }
}
