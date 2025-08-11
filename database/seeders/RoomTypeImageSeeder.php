<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeImageSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('room_type_images')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('room_type_images')->insert([
            [
                'room_type_id' => 1, // Tipe Standard
                'image_url' => '192.168.251.106:8000/assets/images/room_standard_cover.jpg',
                'type' => 'cover',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 1,
                'image_url' => '192.168.251.106:8000/assets/images/room_standard_interior.jpg',
                'type' => 'interior',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2, // Tipe Deluxe AC
                'image_url' => '192.168.251.106:8000/assets/images/room_deluxe_cover.jpg',
                'type' => 'cover',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2,
                'image_url' => '192.168.251.106:8000/assets/images/room_deluxe_bathroom.jpg',
                'type' => 'bathroom',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}