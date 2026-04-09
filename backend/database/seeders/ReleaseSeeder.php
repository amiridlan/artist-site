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
                'cover_image' => 'releases/green-flash.jpg',
                'description' => 'KLP48\'s rendition of the iconic AKB48 hit, recorded live at the 1st Anniversary Concert "BLOOM" at Zepp Kuala Lumpur.',
                'tracks' => [['number' => 1, 'title' => 'Green Flash']],
            ],
            [
                'slug' => 'oh-my-pumpkin',
                'title' => 'Oh My Pumpkin!',
                'type' => 'single',
                'release_date' => '2025-08-13',
                'cover_image' => 'releases/oh-my-pumpkin.jpg',
                'description' => 'A fun and energetic single performed at the 1st Anniversary Concert "BLOOM".',
                'tracks' => [['number' => 1, 'title' => 'Oh My Pumpkin!']],
            ],
            [
                'slug' => 'first-cry',
                'title' => 'First Cry',
                'type' => 'ep',
                'release_date' => '2025-06-13',
                'cover_image' => 'releases/first-cry.jpg',
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
                'cover_image' => 'releases/flying-get.png',
                'description' => 'A high-energy cover of AKB48\'s legendary summer anthem.',
                'tracks' => [['number' => 1, 'title' => 'Flying Get']],
            ],
            [
                'slug' => 'heavy-rotation',
                'title' => 'Heavy Rotation',
                'type' => 'single',
                'release_date' => '2024-09-01',
                'cover_image' => 'releases/heavy-rotation.png',
                'description' => 'KLP48\'s cover of AKB48\'s iconic Heavy Rotation.',
                'tracks' => [['number' => 1, 'title' => 'Heavy Rotation']],
            ],
            [
                'slug' => 'alasanku-maybe',
                'title' => 'Alasanku Maybe',
                'type' => 'single',
                'release_date' => '2025-03-15',
                'cover_image' => 'releases/alasanku-maybe.png',
                'description' => 'KLP48\'s original single.',
                'tracks' => [['number' => 1, 'title' => 'Alasanku Maybe']],
            ],
        ];

        foreach ($releases as $release) {
            Release::updateOrCreate(['slug' => $release['slug']], $release);
        }
    }
}
