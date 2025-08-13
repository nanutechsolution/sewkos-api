<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyFacilitySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('property_facilities')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Fasilitas untuk Kos Sumba Indah (property_id 1)
        DB::table('property_facilities')->insert([
            ['property_id' => 1, 'facility_id' => 1], // Wi-Fi
            ['property_id' => 1, 'facility_id' => 2], // Dapur Bersama
            ['property_id' => 1, 'facility_id' => 5], // Penjaga Kos
        ]);

        // Fasilitas untuk Homestay Waingapu View (property_id 2)
        DB::table('property_facilities')->insert([
            ['property_id' => 2, 'facility_id' => 1], // Wi-Fi
            ['property_id' => 2, 'facility_id' => 3], // Ruang Tamu
            ['property_id' => 2, 'facility_id' => 6], // CCTV
        ]);
    }
}
