<?php

namespace Database\Seeders;

use App\Models\FanclubMember;
use Illuminate\Database\Seeder;

class FanclubSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            // 2024 signups
            ['name' => 'Nurul Ain',      'email' => 'nurulain@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2024-09-01', 'expires_at' => '2025-09-01', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Wei Jie',        'email' => 'weijie@example.com',      'tier' => 'gold',  'status' => 'active',    'joined_at' => '2024-09-15', 'expires_at' => '2025-09-15', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Priya Nair',     'email' => 'priya@example.com',       'tier' => 'basic', 'status' => 'expired',   'joined_at' => '2024-10-01', 'expires_at' => '2025-10-01', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Amir Hamzah',    'email' => 'amirhamzah@example.com',  'tier' => 'gold',  'status' => 'active',    'joined_at' => '2024-10-20', 'expires_at' => '2025-10-20', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Siti Hajar',     'email' => 'sitihajar@example.com',   'tier' => 'basic', 'status' => 'active',    'joined_at' => '2024-11-05', 'expires_at' => '2025-11-05', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Darren Lim',     'email' => 'darrenlim@example.com',   'tier' => 'basic', 'status' => 'cancelled', 'joined_at' => '2024-11-20', 'expires_at' => null,         'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Farah Diyana',   'email' => 'farahdiyana@example.com', 'tier' => 'gold',  'status' => 'active',    'joined_at' => '2024-12-01', 'expires_at' => '2025-12-01', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Kelvin Tan',     'email' => 'kelvintan@example.com',   'tier' => 'basic', 'status' => 'active',    'joined_at' => '2024-12-15', 'expires_at' => '2025-12-15', 'benefits' => ['Newsletter', 'Digital wallpaper']],

            // 2025 signups
            ['name' => 'Ili Nadhirah',   'email' => 'ilinadhirah@example.com', 'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-01-10', 'expires_at' => '2026-01-10', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Joshua Wong',    'email' => 'joshuawong@example.com',  'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-01-22', 'expires_at' => '2026-01-22', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Nur Izzati',     'email' => 'nurizzati@example.com',   'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-02-05', 'expires_at' => '2026-02-05', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Hafiz Roslan',   'email' => 'hafizroslan@example.com', 'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-02-18', 'expires_at' => '2026-02-18', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Chong Mei Ling', 'email' => 'meilingc@example.com',    'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-03-01', 'expires_at' => '2026-03-01', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Ravin Kumar',    'email' => 'ravink@example.com',      'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-03-14', 'expires_at' => '2026-03-14', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Nabilah Zain',   'email' => 'nabilahz@example.com',    'tier' => 'basic', 'status' => 'expired',   'joined_at' => '2025-04-02', 'expires_at' => '2026-04-02', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Bryan Lee',      'email' => 'bryanlee@example.com',    'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-04-20', 'expires_at' => '2026-04-20', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Aisyah Putri',   'email' => 'aisyahputri@example.com', 'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-05-08', 'expires_at' => '2026-05-08', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Zulaikha Md',    'email' => 'zulaikha@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-05-25', 'expires_at' => '2026-05-25', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Marcus Ho',      'email' => 'marcusho@example.com',    'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-06-10', 'expires_at' => '2026-06-10', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Syafiqah Rahim', 'email' => 'syafiqah@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-06-28', 'expires_at' => '2026-06-28', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Tan Hui Shan',   'email' => 'huishan@example.com',     'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-07-15', 'expires_at' => '2026-07-15', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Haziq Azlan',    'email' => 'haziqazlan@example.com',  'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-08-01', 'expires_at' => '2026-08-01', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Fiona Yap',      'email' => 'fionayap@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-08-19', 'expires_at' => '2026-08-19', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Mohd Ridhwan',   'email' => 'ridhwan@example.com',     'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-09-03', 'expires_at' => '2026-09-03', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Amanda Ong',     'email' => 'amandaong@example.com',   'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-09-20', 'expires_at' => '2026-09-20', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Syarifah Nur',   'email' => 'syarifahnur@example.com', 'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-10-07', 'expires_at' => '2026-10-07', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Jason Chew',     'email' => 'jasonchew@example.com',   'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-10-22', 'expires_at' => '2026-10-22', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Liyana Zaidi',   'email' => 'liyanaz@example.com',     'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-11-05', 'expires_at' => '2026-11-05', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Ethan Koh',      'email' => 'ethankoh@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-11-18', 'expires_at' => '2026-11-18', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Nur Farhana',    'email' => 'nurfarhana@example.com',  'tier' => 'basic', 'status' => 'active',    'joined_at' => '2025-12-01', 'expires_at' => '2026-12-01', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Reuben Raj',     'email' => 'reubenraj@example.com',   'tier' => 'gold',  'status' => 'active',    'joined_at' => '2025-12-20', 'expires_at' => '2026-12-20', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],

            // 2026 signups
            ['name' => 'Izzatul Husna',  'email' => 'izzatul@example.com',     'tier' => 'basic', 'status' => 'active',    'joined_at' => '2026-01-08', 'expires_at' => '2027-01-08', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Kenneth Sim',    'email' => 'kensimon@example.com',    'tier' => 'gold',  'status' => 'active',    'joined_at' => '2026-01-25', 'expires_at' => '2027-01-25', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Noor Aqilah',    'email' => 'nooraqilah@example.com',  'tier' => 'basic', 'status' => 'active',    'joined_at' => '2026-02-10', 'expires_at' => '2027-02-10', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Sean Lim',       'email' => 'seanlim@example.com',     'tier' => 'basic', 'status' => 'active',    'joined_at' => '2026-02-24', 'expires_at' => '2027-02-24', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Hanis Nazirah',  'email' => 'hanisn@example.com',      'tier' => 'gold',  'status' => 'active',    'joined_at' => '2026-03-05', 'expires_at' => '2027-03-05', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
            ['name' => 'Vincent Leong',  'email' => 'vincentl@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2026-03-18', 'expires_at' => '2027-03-18', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Athirah Sazali', 'email' => 'athirahs@example.com',    'tier' => 'basic', 'status' => 'active',    'joined_at' => '2026-04-01', 'expires_at' => '2027-04-01', 'benefits' => ['Newsletter', 'Digital wallpaper']],
            ['name' => 'Dylan Ng',       'email' => 'dylang@example.com',      'tier' => 'gold',  'status' => 'active',    'joined_at' => '2026-04-07', 'expires_at' => '2027-04-07', 'benefits' => ['Newsletter', 'Digital wallpaper', 'Priority ticketing', 'Exclusive merch discount']],
        ];

        foreach ($members as $member) {
            // Add default password for all seeded members (hashed by model cast)
            $member['password'] = 'password';
            FanclubMember::updateOrCreate(['email' => $member['email']], $member);
        }
    }
}
