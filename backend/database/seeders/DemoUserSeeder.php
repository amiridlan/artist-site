<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@klp48.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 2. Marketing Department Users
        $marketing1 = User::updateOrCreate(
            ['email' => 'marketing1@klp48.com'],
            [
                'name' => 'Marketing Staff 1',
                'password' => Hash::make('password'),
            ]
        );
        $marketing1->assignRole('Marketing Department');

        $marketing2 = User::updateOrCreate(
            ['email' => 'marketing2@klp48.com'],
            [
                'name' => 'Marketing Staff 2',
                'password' => Hash::make('password'),
            ]
        );
        $marketing2->assignRole('Marketing Department');

        // 3. Events Department Users
        $events1 = User::updateOrCreate(
            ['email' => 'events1@klp48.com'],
            [
                'name' => 'Events Staff 1',
                'password' => Hash::make('password'),
            ]
        );
        $events1->assignRole('Events Department');

        $events2 = User::updateOrCreate(
            ['email' => 'events2@klp48.com'],
            [
                'name' => 'Events Staff 2',
                'password' => Hash::make('password'),
            ]
        );
        $events2->assignRole('Events Department');

        // 4. Artist Users (linked to Member records)
        $yiShyanMember = Member::where('slug', 'yi-shyan')->first();
        if ($yiShyanMember) {
            $yiShyanUser = User::updateOrCreate(
                ['email' => 'yishyan@klp48.com'],
                [
                    'name' => 'Yi Shyan',
                    'password' => Hash::make('password'),
                    'member_id' => $yiShyanMember->id,
                ]
            );
            $yiShyanUser->assignRole('Artist');
        }

        $tiffanyMember = Member::where('slug', 'tiffany')->first();
        if ($tiffanyMember) {
            $tiffanyUser = User::updateOrCreate(
                ['email' => 'tiffany@klp48.com'],
                [
                    'name' => 'Tiffany',
                    'password' => Hash::make('password'),
                    'member_id' => $tiffanyMember->id,
                ]
            );
            $tiffanyUser->assignRole('Artist');
        }

        $salwaMember = Member::where('slug', 'salwa')->first();
        if ($salwaMember) {
            $salwaUser = User::updateOrCreate(
                ['email' => 'salwa@klp48.com'],
                [
                    'name' => 'Salwa',
                    'password' => Hash::make('password'),
                    'member_id' => $salwaMember->id,
                ]
            );
            $salwaUser->assignRole('Artist');
        }
    }
}
