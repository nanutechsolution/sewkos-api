<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyImageSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('property_images')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('property_images')->insert([
            [
                'property_id' => 1,
                'image_url' => '/assets/images/kos_sumba_1.jpg',
                'type' => 'front_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 1,
                'image_url' => '/assets/images/kos_sumba_interior.jpg',
                'type' => 'interior',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => 2,
                'image_url' => '/assets/images/kos_sumba_2.jpg',
                'type' => 'front_view',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
