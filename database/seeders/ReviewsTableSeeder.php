<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reviews')->insert([
            [
                'kos_id' => 1, // Pastikan ID ini ada di tabel kos Anda
                'author_name' => 'Budi Santoso',
                'comment' => 'Kosnya bersih dan fasilitasnya lengkap. Lokasi strategis dekat dengan pusat kota.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kos_id' => 1,
                'author_name' => 'Citra Dewi',
                'comment' => 'Harga terjangkau, tapi kamar agak kecil.',
                'rating' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kos_id' => 2,
                'author_name' => 'Faisal Ahmad',
                'comment' => 'Pemandangan dari kos sangat indah. Cocok untuk bersantai.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kos_id' => 3,
                'author_name' => 'Faisal Ahmad',
                'comment' => 'Pemandangan dari kos sangat indah. Cocok untuk bersantai.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kos_id' => 4,
                'author_name' => 'Faisal Ahmad',
                'comment' => 'Pemandangan dari kos sangat indah. Cocok untuk bersantai.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}