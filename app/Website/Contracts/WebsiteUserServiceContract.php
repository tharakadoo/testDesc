<?php

namespace App\Website\Contracts;

use Illuminate\Support\Collection;
interface WebsiteUserServiceContract
{
    public function getUsersForWebsite(int $websiteId): Collection;
}
