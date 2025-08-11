<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('facilities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $facilities = [
            // Fasilitas Umum
            ['name' => 'Wi-Fi', 'type' => 'umum'],
            ['name' => 'Dapur Bersama', 'type' => 'umum'],
            ['name' => 'Ruang Tamu', 'type' => 'umum'],
            ['name' => 'Laundry', 'type' => 'umum'],
            ['name' => 'Penjaga Kos', 'type' => 'umum'],
            ['name' => 'CCTV', 'type' => 'umum'],

            // Fasilitas Kamar
            ['name' => 'AC', 'type' => 'kamar'],
            ['name' => 'Kipas Angin', 'type' => 'kamar'],
            ['name' => 'Kasur', 'type' => 'kamar'],
            ['name' => 'Meja Belajar', 'type' => 'kamar'],
            ['name' => 'Lemari Pakaian', 'type' => 'kamar'],
            ['name' => 'TV', 'type' => 'kamar'],

            // Fasilitas Kamar Mandi
            ['name' => 'Kamar Mandi Dalam', 'type' => 'kamar_mandi'],
            ['name' => 'Shower', 'type' => 'kamar_mandi'],
            ['name' => 'Kloset Duduk', 'type' => 'kamar_mandi'],
            ['name' => 'Ember Mandi', 'type' => 'kamar_mandi'],

            // Fasilitas Parkir
            ['name' => 'Parkir Motor', 'type' => 'parkir'],
            ['name' => 'Parkir Mobil', 'type' => 'parkir'],
        ];

        DB::table('facilities')->insert($facilities);
    }
}