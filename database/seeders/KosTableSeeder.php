<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KosTableSeeder extends Seeder
{
    public function run()
    {

        DB::table('kos')->insert([
            [
                'user_id' => 1,
                'name' => 'Kos Sumba Indah',
                'location' => 'Waikabubak',
                'latitude' => -9.654876,
                'longitude' => 119.393921,
                'price' => '500000',
                'description' => 'Kos ini berada di pusat kota Waikabubak, ideal untuk mahasiswa dan pekerja.',
                'image_url' => 'http://192.168.93.106:8000/assets/images/kos_sumba_1.jpg',
                'facilities' => json_encode(['Wi-Fi', 'Kamar Mandi Dalam', 'Dapur Bersama']),
                'status' => 'kosong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Kos Waingapu Sentral',
                'location' => 'Waingapu',
                'latitude' => -9.664440,
                'longitude' => 120.252033,
                'price' => '650000',
                'description' => 'Kos dengan pemandangan laut di Waingapu, dekat pelabuhan.',
                'image_url' => 'http://192.168.93.106:8000/assets/images/kos_sumba_2.jpg',
                'facilities' => json_encode(['Wi-Fi', 'AC']),
                'status' => 'kosong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Homestay Lapangan',
                'location' => 'Waikabubak',
                'latitude' => -9.652150,
                'longitude' => 119.389500,
                'price' => '450000',
                'description' => 'Penginapan sederhana di dekat lapangan olahraga, lingkungan yang tenang.',
                'image_url' => 'http://192.168.93.106:8000/assets/images/kos_sumba_1.jpg',
                'facilities' => json_encode(['Dapur Bersama', 'Tempat Parkir']),
                'status' => 'kosong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Kos Dekat Bandara',
                'location' => 'Tambolaka',
                'latitude' => -9.407980,
                'longitude' => 119.066490,
                'price' => '700000',
                'description' => 'Kos strategis di dekat Bandara Tambolaka, cocok untuk staf bandara.',
                'image_url' => 'http://192.168.93.106:8000/assets/images/kos_sumba_2.jpg',
                'facilities' => json_encode(['AC', 'Wi-Fi']),
                'status' => 'kosong',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Kos Dekat Pantai',
                'location' => 'Pantai Nihiwatu',
                'latitude' => -9.734500,
                'longitude' => 119.011200,
                'price' => '1200000',
                'description' => 'Kos eksklusif dengan akses mudah ke Pantai Nihiwatu.',
                'image_url' => 'http://192.168.93.106:8000/assets/images/kos_sumba_1.jpg',
                'facilities' => json_encode(['AC', 'Kamar Mandi Dalam', 'Kolam Renang']),
                'status' => 'terisi', // Contoh kos terisi
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}