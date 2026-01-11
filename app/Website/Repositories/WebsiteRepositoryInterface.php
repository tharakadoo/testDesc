<?php

namespace App\Website\Repositories;

use App\Website\Entities\Website;
use Illuminate\Support\Collection;

interface WebsiteRepositoryInterface
{
    public function find(int $websiteId): ?Website;

    public function all(): Collection;
}
