<?php

namespace App\Website\Repositories;

use App\Website\Entities\Website;

interface WebsiteRepositoryInterface
{
    public function find(int $websiteId): ?Website;
}
