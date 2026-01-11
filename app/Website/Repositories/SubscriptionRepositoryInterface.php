<?php

namespace App\Website\Repositories;

use App\User\Entities\User;
use App\Website\Entities\Website;

interface SubscriptionRepositoryInterface
{
    public function isSubscribed(User $user, Website $website): bool;

    public function subscribe(User $user, Website $website): void;
}
