<?php

namespace Database\Seeders;

use App\Website\Entities\Website;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $websites = [
            ['url' => 'https://techcrunch.com'],
            ['url' => 'https://theverge.com'],
            ['url' => 'https://wired.com'],
            ['url' => 'https://arstechnica.com'],
            ['url' => 'https://engadget.com'],
        ];

        foreach ($websites as $website) {
            Website::firstOrCreate($website);
        }
    }
}
