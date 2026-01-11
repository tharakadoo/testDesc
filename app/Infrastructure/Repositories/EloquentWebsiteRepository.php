<?php

namespace App\Infrastructure\Repositories;

use App\Website\Entities\Website;
use App\Website\Repositories\WebsiteRepositoryInterface;
use Illuminate\Support\Collection;

final class EloquentWebsiteRepository implements WebsiteRepositoryInterface
{
    public function find(int $websiteId): ?Website
    {
        return Website::find($websiteId);
    }

    public function all(): Collection
    {
        return Website::all();
    }
}
