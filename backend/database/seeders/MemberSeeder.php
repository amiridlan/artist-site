<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['slug' => 'cocoa', 'name_english' => 'Cocoa', 'generation' => '1st', 'status' => 'concluded', 'color' => '#F4A7BB', 'sort_order' => 1],
            ['slug' => 'suzuha', 'name_english' => 'Suzuha', 'generation' => '1st', 'status' => 'concluded', 'color' => '#87CEEB', 'sort_order' => 2],
            ['slug' => 'haruka', 'name_english' => 'Haruka', 'generation' => '1st', 'status' => 'concluded', 'color' => '#FFB347', 'sort_order' => 3],
            ['slug' => 'yurina', 'name_english' => 'Yurina', 'generation' => '1st', 'status' => 'concluded', 'color' => '#DDA0DD', 'sort_order' => 4],
            ['slug' => 'airi', 'name_english' => 'Airi', 'generation' => '1st', 'status' => 'active', 'color' => '#00B4A0', 'sort_order' => 5],
            ['slug' => 'miyu', 'name_english' => 'Miyu', 'generation' => '1st', 'status' => 'active', 'color' => '#C9A84C', 'sort_order' => 6],
            ['slug' => 'sakura', 'name_english' => 'Sakura', 'generation' => '1st', 'status' => 'active', 'color' => '#F4A7BB', 'sort_order' => 7],
            ['slug' => 'hana', 'name_english' => 'Hana', 'generation' => '1st', 'status' => 'active', 'color' => '#FF6B6B', 'sort_order' => 8],
            ['slug' => 'rina', 'name_english' => 'Rina', 'generation' => '1st', 'status' => 'active', 'color' => '#4ECDC4', 'sort_order' => 9],
            ['slug' => 'yuki', 'name_english' => 'Yuki', 'generation' => '1st', 'status' => 'active', 'color' => '#95E1D3', 'sort_order' => 10],
            ['slug' => 'mei', 'name_english' => 'Mei', 'generation' => '2nd', 'status' => 'active', 'color' => '#F38181', 'sort_order' => 11],
            ['slug' => 'sora', 'name_english' => 'Sora', 'generation' => '2nd', 'status' => 'active', 'color' => '#AA96DA', 'sort_order' => 12],
            ['slug' => 'nana', 'name_english' => 'Nana', 'generation' => '2nd', 'status' => 'active', 'color' => '#FCBAD3', 'sort_order' => 13],
            ['slug' => 'kaho', 'name_english' => 'Kaho', 'generation' => '2nd', 'status' => 'active', 'color' => '#A8D8EA', 'sort_order' => 14],
            ['slug' => 'riko', 'name_english' => 'Riko', 'generation' => '2nd', 'status' => 'active', 'color' => '#FFD3B6', 'sort_order' => 15],
            ['slug' => 'mio', 'name_english' => 'Mio', 'generation' => '2nd', 'status' => 'active', 'color' => '#D5AAFF', 'sort_order' => 16],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }
    }
}
