<?php

namespace Database\Seeders;

use App\User\Entities\User;

class UserSeeder
{
    public function run(): void
    {
        foreach (range(1, 5) as $id) {
            User::factory()->create(['id' => $id]);
        }
    }

}
