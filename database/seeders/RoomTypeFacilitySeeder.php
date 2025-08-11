<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeFacilitySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('room_type_facilities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Fasilitas untuk Tipe Standard (room_type_id 1)
        DB::table('room_type_facilities')->insert([
            ['room_type_id' => 1, 'facility_id' => 7],  // AC
            ['room_type_id' => 1, 'facility_id' => 9],  // Kasur
            ['room_type_id' => 1, 'facility_id' => 10], // Meja Belajar
        ]);

        // Fasilitas untuk Tipe Deluxe AC (room_type_id 2)
        DB::table('room_type_facilities')->insert([
            ['room_type_id' => 2, 'facility_id' => 7],  // AC
            ['room_type_id' => 2, 'facility_id' => 9],  // Kasur
            ['room_type_id' => 2, 'facility_id' => 12], // Kamar Mandi Dalam
        ]);

        // Fasilitas untuk Kamar Pemandangan Laut (room_type_id 3)
        DB::table('room_type_facilities')->insert([
            ['room_type_id' => 3, 'facility_id' => 8],  // Kipas Angin
            ['room_type_id' => 3, 'facility_id' => 9],  // Kasur
        ]);
    }
}