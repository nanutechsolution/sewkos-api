<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('rooms')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Kamar untuk Tipe Standard (property_id 1, room_type_id 1)
        for ($i = 1; $i <= 5; $i++) {
            DB::table('rooms')->insert([
                'room_type_id' => 1,
                'room_number' => 'Std-' . $i,
                'floor' => 1,
                'status' => ($i <= 3) ? 'available' : 'occupied', // 3 available, 2 occupied
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kamar untuk Tipe Deluxe AC (property_id 1, room_type_id 2)
        for ($i = 1; $i <= 3; $i++) {
            DB::table('rooms')->insert([
                'room_type_id' => 2,
                'room_number' => 'Deluxe-' . $i,
                'floor' => 2,
                'status' => ($i == 1) ? 'available' : 'occupied', // 1 available, 2 occupied
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Kamar untuk Kamar Pemandangan Laut (property_id 2, room_type_id 3)
        for ($i = 1; $i <= 2; $i++) {
            DB::table('rooms')->insert([
                'room_type_id' => 3,
                'room_number' => 'SeaView-' . $i,
                'floor' => 1,
                'status' => 'available', // Semua available
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}