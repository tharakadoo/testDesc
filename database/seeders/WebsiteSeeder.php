<?php

namespace Database\Seeders;

use App\Website\Entities\Website;

class WebsiteSeeder
{
    public function run(): void
    {
        Website::factory()->count(5)->create();
    }
}
