<?php

namespace App\Website\IO\Database;

use App\Website\UseCases\WebsiteUserServiceContract;
use App\Website\Entities\Website;
use Illuminate\Support\Collection;

final class EloquentWebsiteUserService implements WebsiteUserServiceContract
{
    public function getUsersForWebsite(int $websiteId): Collection
    {
        return Website::findOrFail($websiteId)->users()->get();
    }
}
