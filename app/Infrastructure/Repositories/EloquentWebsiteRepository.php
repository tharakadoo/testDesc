<?php

namespace App\Infrastructure\Repositories;

use App\Website\Entities\Website;
use App\Website\Repositories\WebsiteRepositoryInterface;

final class EloquentWebsiteRepository implements WebsiteRepositoryInterface
{
    public function find(int $websiteId): ?Website
    {
        return Website::find($websiteId);
    }
}
