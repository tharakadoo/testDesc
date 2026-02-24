<?php

namespace App\Foundation\IO\Database\seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(\App\Website\IO\Database\seeders\WebsiteSeeder::class);
        $this->call(\App\User\IO\Database\seeders\UserSeeder::class);
    }
}
