<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('properties')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('properties')->insert([
            [
                'user_id' => 1, // Pastikan user_id ini ada di tabel users
                'name' => 'Kos Sumba Indah',
                'gender_preference' => 'Campur',
                'description' => 'Kos nyaman di pusat kota Waikabubak, dekat kampus dan fasilitas umum. Lingkungan aman dan bersih.',
                'rules' => 'Tidak boleh membawa hewan peliharaan. Jam malam pukul 23.00.',
                'rules_file_url' => null,
                'year_built' => 2018,
                'manager_name' => 'Bapak Budi',
                'manager_phone' => '081234567890',
                'notes' => 'Tersedia dapur bersama di lantai 1.',
                'address_street' => 'Jl. Pendidikan No. 10',
                'address_city' => 'Waikabubak',
                'address_province' => 'Nusa Tenggara Timur',
                'address_zip_code' => '87211',
                'latitude' => -9.654876,
                'longitude' => 119.393921,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Homestay Waingapu View',
                'gender_preference' => 'Putri',
                'description' => 'Homestay khusus putri dengan pemandangan laut, suasana tenang dan nyaman. Cocok untuk mahasiswi dan pekerja.',
                'rules' => 'Dilarang membawa tamu pria ke dalam kamar. Wajib lapor jika menginap.',
                'rules_file_url' => null,
                'year_built' => 2020,
                'manager_name' => 'Ibu Siti',
                'manager_phone' => '081298765432',
                'notes' => 'Tersedia layanan laundry berbayar.',
                'address_street' => 'Jl. Pelabuhan Lama No. 5',
                'address_city' => 'Waingapu',
                'address_province' => 'Nusa Tenggara Timur',
                'address_zip_code' => '87111',
                'latitude' => -9.664440,
                'longitude' => 120.252033,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
