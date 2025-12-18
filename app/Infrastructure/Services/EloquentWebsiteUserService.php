<?php

namespace App\Infrastructure\Services;

use App\Website\Contracts\WebsiteUserServiceContract;
use App\Website\Entities\Website;
use Illuminate\Support\Collection;

final class EloquentWebsiteUserService implements WebsiteUserServiceContract
{
    public function getUsersForWebsite(int $websiteId): Collection
    {
        return Website::findOrFail($websiteId)->users()->get();
    }
}
