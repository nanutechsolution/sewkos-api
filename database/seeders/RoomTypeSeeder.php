<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('room_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('room_types')->insert([
            [
                'property_id' => 1, // Kos Sumba Indah
                'name' => 'Tipe Standard',
                'description' => 'Kamar standar dengan fasilitas dasar.',
                'size_m2' => 9.0, // 3x3 meter
                'total_rooms' => 5,
                'available_rooms' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 1, // Kos Sumba Indah
                'name' => 'Tipe Deluxe AC',
                'description' => 'Kamar lebih luas dengan AC dan kamar mandi dalam.',
                'size_m2' => 12.0, // 3x4 meter
                'total_rooms' => 3,
                'available_rooms' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 2, // Homestay Waingapu View
                'name' => 'Kamar Pemandangan Laut',
                'description' => 'Kamar dengan balkon menghadap laut.',
                'size_m2' => 10.0,
                'total_rooms' => 2,
                'available_rooms' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}