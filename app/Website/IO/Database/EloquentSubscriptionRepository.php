<?php

namespace App\Website\IO\Database;

use App\User\Entities\User;
use App\Website\Entities\Website;
use App\Website\UseCases\Repositories\SubscriptionRepositoryInterface;

final class EloquentSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function isSubscribed(User $user, Website $website): bool
    {
        return $website->users()->where('user_id', $user->id)->exists();
    }

    public function subscribe(User $user, Website $website): void
    {
        $website->users()->attach($user->id);
    }
}
