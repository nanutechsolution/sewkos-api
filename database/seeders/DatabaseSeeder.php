<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Owner Sumba',
            'email' => 'owner@kossumba.com',
        ]);
        $this->call(KosTableSeeder::class);
        $this->call(ReviewsTableSeeder::class);
    }
}
