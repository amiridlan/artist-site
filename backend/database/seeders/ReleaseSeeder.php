<?php

namespace Database\Seeders;

use App\Models\Release;
use Illuminate\Database\Seeder;

class ReleaseSeeder extends Seeder
{
    public function run(): void
    {
        $releases = [
            [
                'slug' => 'green-flash',
                'title' => 'Green Flash',
                'type' => 'single',
                'release_date' => '2025-09-12',
                'description' => 'KLP48\'s rendition of the iconic AKB48 hit, recorded live at the 1st Anniversary Concert "BLOOM" at Zepp Kuala Lumpur.',
                'tracks' => [['number' => 1, 'title' => 'Green Flash']],
            ],
            [
                'slug' => 'oh-my-pumpkin',
                'title' => 'Oh my pumpkin!',
                'type' => 'single',
                'release_date' => '2025-08-13',
                'description' => 'A fun and energetic single performed at the 1st Anniversary Concert "BLOOM".',
                'tracks' => [['number' => 1, 'title' => 'Oh my pumpkin!']],
            ],
            [
                'slug' => 'first-cry',
                'title' => 'First Cry',
                'type' => 'ep',
                'release_date' => '2025-06-13',
                'description' => 'KLP48\'s debut EP featuring a collection of songs showcasing the group\'s vocal range and emotional depth.',
                'tracks' => [
                    ['number' => 1, 'title' => 'First Cry'],
                    ['number' => 2, 'title' => 'Aitakatta'],
                    ['number' => 3, 'title' => 'Heavy Rotation'],
                    ['number' => 4, 'title' => 'Shonichi'],
                ],
            ],
            [
                'slug' => 'flying-get',
                'title' => 'Flying Get',
                'type' => 'single',
                'release_date' => '2025-05-30',
                'description' => 'A high-energy cover of AKB48\'s legendary summer anthem.',
                'tracks' => [['number' => 1, 'title' => 'Flying Get']],
            ],
            [
                'slug' => 'kerana-kusuka',
                'title' => 'Kerana Kusuka',
                'type' => 'single',
                'release_date' => '2026-03-01',
                'description' => 'KLP48\'s upcoming original single. Release has been postponed — new date to be announced.',
                'tracks' => [['number' => 1, 'title' => 'Kerana Kusuka']],
            ],
        ];

        foreach ($releases as $release) {
            Release::create($release);
        }
    }
}
