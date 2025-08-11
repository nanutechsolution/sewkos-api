<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Nonaktifkan FK checks sementara
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Aktifkan kembali

        DB::table('users')->insert([
            'name' => 'Pemilik Kos Sumba',
            'email' => 'owner@kossumba.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}