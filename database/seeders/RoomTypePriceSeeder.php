<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypePriceSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Nonaktifkan FK checks sementara
        DB::table('room_type_prices')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Aktifkan kembali

        DB::table('room_type_prices')->insert([
            // Harga untuk Tipe Standard (room_type_id 1)
            [
                'room_type_id' => 1,
                'period_type' => 'daily',
                'price' => 75000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 1,
                'period_type' => 'monthly',
                'price' => 750000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 1,
                'period_type' => '3_months',
                'price' => 2100000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 1,
                'period_type' => '6_months',
                'price' => 4000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Harga untuk Tipe Deluxe AC (room_type_id 2)
            [
                'room_type_id' => 2,
                'period_type' => 'daily',
                'price' => 120000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2,
                'period_type' => 'monthly',
                'price' => 1200000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2,
                'period_type' => '3_months',
                'price' => 3400000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2,
                'period_type' => '6_months',
                'price' => 6500000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Harga untuk Kamar Pemandangan Laut (room_type_id 3)
            [
                'room_type_id' => 3,
                'period_type' => 'daily',
                'price' => 100000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 3,
                'period_type' => 'monthly',
                'price' => 900000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}