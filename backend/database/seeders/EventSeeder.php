<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            ['slug' => 'bloom-1st-anniversary', 'title' => 'KLP48 1st Anniversary Concert "BLOOM"', 'type' => 'concert', 'status' => 'completed', 'date' => '2025-08-16', 'venue' => 'Zepp Kuala Lumpur', 'location' => 'Kuala Lumpur, Malaysia', 'description' => 'KLP48\'s monumental 1st Anniversary Concert featuring special performances, new song reveals, and a celebration of the group\'s first year.'],
            ['slug' => 'super-bash-solo-concert', 'title' => 'KLP48 First Solo Concert "Super Bash"', 'type' => 'concert', 'status' => 'upcoming', 'date' => '2026-04-15', 'venue' => 'TBA', 'location' => 'Kuala Lumpur, Malaysia', 'description' => 'KLP48\'s first ever solo concert! Stay tuned for venue and ticket details.'],
            ['slug' => 'cocoa-meet-greet-cancelled', 'title' => 'Cocoa Meet & Greet Session', 'type' => 'meet-greet', 'status' => 'cancelled', 'date' => '2025-12-20', 'venue' => 'KLP48 Theater', 'location' => 'Kuala Lumpur, Malaysia', 'description' => 'Meet & Greet session with Cocoa. This event has been cancelled. Refund information has been announced.'],
            ['slug' => 'discord-radio-jan-2026', 'title' => 'Discord Radio Talk - January 2026', 'type' => 'online', 'status' => 'completed', 'date' => '2026-01-06', 'description' => 'Monthly Discord Radio Talk with KLP48 members. Fan club members can interact directly with the members.'],
            ['slug' => 'debut-stage', 'title' => 'KLP48 Debut Stage', 'type' => 'concert', 'status' => 'completed', 'date' => '2024-08-16', 'venue' => 'Zepp Kuala Lumpur', 'location' => 'Kuala Lumpur, Malaysia', 'description' => 'The historic debut stage of KLP48 at Zepp Kuala Lumpur, marking the birth of Malaysia\'s AKB48 sister group.'],
            ['slug' => 'handshake-spring-2026', 'title' => 'Handshake Event - Spring 2026', 'type' => 'handshake', 'status' => 'upcoming', 'date' => '2026-05-10', 'venue' => 'TBA', 'location' => 'Kuala Lumpur, Malaysia', 'description' => 'Upcoming handshake event with KLP48 members. Ticket details to be announced.'],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
