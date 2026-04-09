<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@klp48.my')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@klp48.my',
            ]);
        }

        $this->call([
            MemberSeeder::class,
            NewsSeeder::class,
            ReleaseSeeder::class,
            VideoSeeder::class,
            EventSeeder::class,
            FanclubSeeder::class,
        ]);
    }
}
