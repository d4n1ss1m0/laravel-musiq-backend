<?php

namespace Database\Seeders;

use App\Models\PlaylistType;
use App\Shared\Enums\PlaylistTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaylistTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = PlaylistTypes::cases();

        $data = [];

        foreach ($types as $type) {
            PlaylistType::firstOrCreate([
                'name' => $type->value
            ]);
        }
    }
}
