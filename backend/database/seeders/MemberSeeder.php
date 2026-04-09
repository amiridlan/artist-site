<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            // 1st Generation
            ['slug' => 'yi-shyan',  'name_english' => 'Yi Shyan',  'generation' => '1st', 'status' => 'active', 'photo' => 'members/yi-shyan.jpg',  'sort_order' => 1],
            ['slug' => 'amanda',    'name_english' => 'Amanda',    'generation' => '1st', 'status' => 'active', 'photo' => 'members/amanda.jpg',    'sort_order' => 2],
            ['slug' => 'salwa',     'name_english' => 'Salwa',     'generation' => '1st', 'status' => 'active', 'photo' => 'members/salwa.jpg',     'sort_order' => 3],
            ['slug' => 'anndrea',   'name_english' => 'AnnDrea',   'generation' => '1st', 'status' => 'active', 'photo' => 'members/anndrea.jpg',   'sort_order' => 4],
            ['slug' => 'khalies',   'name_english' => 'Khalies',   'generation' => '1st', 'status' => 'active', 'photo' => 'members/khalies.jpg',   'sort_order' => 5],
            ['slug' => 'hillary',   'name_english' => 'Hillary',   'generation' => '1st', 'status' => 'active', 'photo' => 'members/hillary.jpg',   'sort_order' => 6],
            ['slug' => 'tiffany',   'name_english' => 'Tiffany',   'generation' => '1st', 'status' => 'active', 'photo' => 'members/tiffany.jpg',   'sort_order' => 7],
            ['slug' => 'devi',      'name_english' => 'Devi',      'generation' => '1st', 'status' => 'active', 'photo' => 'members/devi.jpg',      'sort_order' => 8],

            // 2nd Generation
            ['slug' => 'aisha',    'name_english' => 'Aisha',    'generation' => '2nd', 'status' => 'active', 'photo' => 'members/aisha.jpg',    'sort_order' => 9],
            ['slug' => 'alice',    'name_english' => 'Alice',    'generation' => '2nd', 'status' => 'active', 'photo' => 'members/alice.jpg',    'sort_order' => 10],
            ['slug' => 'diva',     'name_english' => 'Diva',     'generation' => '2nd', 'status' => 'active', 'photo' => 'members/diva.jpg',     'sort_order' => 11],
            ['slug' => 'hana',     'name_english' => 'Hana',     'generation' => '2nd', 'status' => 'active', 'photo' => 'members/hana.jpg',     'sort_order' => 12],
            ['slug' => 'isabel',   'name_english' => 'Isabel',   'generation' => '2nd', 'status' => 'active', 'photo' => 'members/isabel.jpg',   'sort_order' => 13],
            ['slug' => 'joo',      'name_english' => 'Joo',      'generation' => '2nd', 'status' => 'active', 'photo' => 'members/joo.jpg',      'sort_order' => 14],
            ['slug' => 'kei',      'name_english' => 'Kei',      'generation' => '2nd', 'status' => 'active', 'photo' => 'members/kei.jpg',      'sort_order' => 15],
            ['slug' => 'maia',     'name_english' => 'Maia',     'generation' => '2nd', 'status' => 'active', 'photo' => 'members/maia.jpg',     'sort_order' => 16],
            ['slug' => 'mashiro',  'name_english' => 'Mashiro',  'generation' => '2nd', 'status' => 'active', 'photo' => 'members/mashiro.jpg',  'sort_order' => 17],
            ['slug' => 'sharleez', 'name_english' => 'Sharleez', 'generation' => '2nd', 'status' => 'active', 'photo' => 'members/sharleez.jpg', 'sort_order' => 18],
            ['slug' => 'shu-zhen', 'name_english' => 'Shu Zhen', 'generation' => '2nd', 'status' => 'active', 'photo' => 'members/shu-zhen.jpg', 'sort_order' => 19],
            ['slug' => 'tara',     'name_english' => 'Tara',     'generation' => '2nd', 'status' => 'active', 'photo' => 'members/tara.jpg',     'sort_order' => 20],
            ['slug' => 'cindy',    'name_english' => 'Cindy',    'generation' => '2nd', 'status' => 'active', 'photo' => 'members/cindy.jpg',    'sort_order' => 21],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(['slug' => $member['slug']], $member);
        }
    }
}
