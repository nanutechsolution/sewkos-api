<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            FacilitySeeder::class,
            PropertySeeder::class,
            PropertyImageSeeder::class,
            RoomTypeSeeder::class,
            RoomTypePriceSeeder::class,
            RoomTypeImageSeeder::class,
            RoomSeeder::class,
            PropertyFacilitySeeder::class,
            RoomTypeFacilitySeeder::class,
            ReviewSeeder::class,
        ]);
    }
}