<?php

namespace App\Website\DataTransferObjects;

use App\User\Entities\User;
use App\Website\Entities\Website;

final readonly class SubscriptionResult
{
    public function __construct(
        public User $user,
        public Website $website,
    ) {}
}
