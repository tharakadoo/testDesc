<?php

namespace App\Website\IO\Database;

use App\Website\Entities\Website;
use App\Website\UseCases\Repositories\WebsiteRepositoryInterface;
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
