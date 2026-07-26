<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            // Venues
            [
                'name' => 'KL Live Performance Hall',
                'type' => 'venue',
                'description' => 'Main performance venue with 500 capacity, full stage lighting and sound system',
                'is_active' => true,
            ],
            [
                'name' => 'Studio A Recording Room',
                'type' => 'venue',
                'description' => 'Professional recording studio with vocal booth and mixing equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Outdoor Event Space',
                'type' => 'venue',
                'description' => 'Open-air venue for outdoor concerts and fan meetings, capacity 1000',
                'is_active' => true,
            ],

            // Equipment
            [
                'name' => 'Sony A7S III Camera',
                'type' => 'equipment',
                'description' => 'Professional 4K video camera for content filming',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Microphone Set (6 units)',
                'type' => 'equipment',
                'description' => 'Shure wireless microphone system for performances',
                'is_active' => true,
            ],
            [
                'name' => 'Stage Lighting Rig',
                'type' => 'equipment',
                'description' => 'Complete LED stage lighting system with controller',
                'is_active' => true,
            ],

            // Vehicles
            [
                'name' => 'Tour Bus (40-seater)',
                'type' => 'vehicle',
                'description' => 'Air-conditioned tour bus for group transportation',
                'is_active' => true,
            ],
            [
                'name' => 'Equipment Van',
                'type' => 'vehicle',
                'description' => 'Cargo van for transporting equipment and props',
                'is_active' => true,
            ],
            [
                'name' => 'Staff MPV',
                'type' => 'vehicle',
                'description' => '7-seater MPV for staff and small group transport',
                'is_active' => true,
            ],
        ];

        foreach ($resources as $resource) {
            Resource::updateOrCreate(
                ['name' => $resource['name']],
                $resource
            );
        }
    }
}
