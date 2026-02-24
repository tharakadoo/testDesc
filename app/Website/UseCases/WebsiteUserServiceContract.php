<?php

namespace App\Website\UseCases;

use Illuminate\Support\Collection;
interface WebsiteUserServiceContract
{
    public function getUsersForWebsite(int $websiteId): Collection;
}
