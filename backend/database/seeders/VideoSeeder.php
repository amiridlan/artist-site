<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            ['slug' => 'green-flash-bloom', 'title' => 'Green Flash - 1st Anniversary Concert "BLOOM"', 'type' => 'performance', 'date' => '2025-08-16', 'venue' => 'Zepp Kuala Lumpur', 'description' => 'Live performance of Green Flash at KLP48\'s 1st Anniversary Concert "BLOOM" in Kuala Lumpur.'],
            ['slug' => 'oh-my-pumpkin-bloom', 'title' => 'Oh my pumpkin! - 1st Anniversary Concert "BLOOM"', 'type' => 'performance', 'date' => '2025-08-16', 'venue' => 'Zepp Kuala Lumpur', 'description' => 'Live performance of Oh my pumpkin! at KLP48\'s 1st Anniversary Concert "BLOOM" in Kuala Lumpur.'],
            ['slug' => 'heavy-rotation-debut', 'title' => 'Heavy Rotation - KLP48 Debut Stage', 'type' => 'performance', 'date' => '2024-08-16', 'venue' => 'Zepp Kuala Lumpur', 'description' => 'KLP48\'s debut stage performance of AKB48\'s iconic Heavy Rotation at Zepp Kuala Lumpur.'],
            ['slug' => 'mv-heavy-rotation', 'title' => '[MV] Heavy Rotation / KLP48', 'type' => 'music-video', 'date' => '2024-09-01', 'description' => 'Official music video for KLP48\'s cover of Heavy Rotation.'],
            ['slug' => 'dp-alasanku-maybe', 'title' => 'Alasanku Maybe - Dance Practice', 'type' => 'dance-practice', 'date' => '2025-03-15', 'description' => 'Dance practice video for Alasanku Maybe.'],
            ['slug' => 'dp-heavy-rotation', 'title' => 'Heavy Rotation - Dance Practice', 'type' => 'dance-practice', 'date' => '2024-10-01', 'description' => 'Dance practice video for Heavy Rotation.'],
            ['slug' => 'dp-shonichi', 'title' => 'Shonichi - Dance Practice', 'type' => 'dance-practice', 'date' => '2024-11-15', 'description' => 'Dance practice video for Shonichi (Day 1).'],
            ['slug' => 'dp-namida-surprise', 'title' => 'Namida Surprise - Dance Practice', 'type' => 'dance-practice', 'date' => '2025-01-20', 'description' => 'Dance practice video for Namida Surprise.'],
            ['slug' => 'dp-high-tension', 'title' => 'High Tension - Dance Practice', 'type' => 'dance-practice', 'date' => '2025-02-10', 'description' => 'Dance practice video for High Tension.'],
            ['slug' => 'dp-aitakatta', 'title' => 'Aitakatta - Dance Practice', 'type' => 'dance-practice', 'date' => '2024-09-20', 'description' => 'Dance practice video for Aitakatta (I Wanted to See You).'],
        ];

        foreach ($videos as $video) {
            Video::updateOrCreate(['slug' => $video['slug']], $video);
        }
    }
}
