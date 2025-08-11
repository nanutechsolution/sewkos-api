<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('reviews')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('reviews')->insert([
            [
                'property_id' => 1, // Untuk Kos Sumba Indah
                'user_id' => 1, // Jika pengulas terdaftar
                'author_name' => 'Pengguna A',
                'comment' => 'Kosnya sangat nyaman dan bersih. Lokasi strategis.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 1,
                'user_id' => null, // Ulasan anonim
                'author_name' => 'Anonim',
                'comment' => 'Harga sesuai dengan fasilitas. Agak bising di malam hari.',
                'rating' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 2, // Untuk Homestay Waingapu View
                'user_id' => 1,
                'author_name' => 'Pengguna B',
                'comment' => 'Pemandangan lautnya indah sekali! Tuan rumah ramah.',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}