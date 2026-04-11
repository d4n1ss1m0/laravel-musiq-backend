<?php

namespace Database\Seeders;

use App\Domain\Entities\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlaylistTypesSeeder::class);
    }
}
