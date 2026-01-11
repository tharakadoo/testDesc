<?php

namespace App\Website\UseCases;

use App\Application\Contracts\TransactionContract;
use App\User\Repositories\UserRepositoryInterface;
use App\Website\DataTransferObjects\SubscribeData;
use App\Website\DataTransferObjects\SubscriptionResult;
use App\Website\Repositories\SubscriptionRepositoryInterface;
use App\Website\Repositories\WebsiteRepositoryInterface;
use Illuminate\Validation\ValidationException;

class SubscribeUseCase
{
    public function __construct(
        private WebsiteRepositoryInterface $website,
        private UserRepositoryInterface $user,
        private SubscriptionRepositoryInterface $subscription,
        private TransactionContract $transaction,
    ) {}

    public function execute(array $subscriptionRequest): SubscriptionResult
    {
        $dto = SubscribeData::fromArray($subscriptionRequest);

        $website = $this->website->find($dto->website_id);

        if (!$website) {
            throw ValidationException::withMessages(['website_id' => ['Website not found']]);
        }

        return $this->transaction->execute(function () use ($dto, $website) {
            $user = $this->user->findOrCreate($dto->email);

            if ($this->subscription->isSubscribed($user, $website)) {
                throw ValidationException::withMessages(['email' => ['Already subscribed to this website']]);
            }

            $this->subscription->subscribe($user, $website);

            return new SubscriptionResult($user, $website);
        });
    }
}
