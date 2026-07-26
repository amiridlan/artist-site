<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Resource;
use App\Models\ScheduleEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class ScheduleEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@klp48.com')->first();
        $eventsStaff = User::where('email', 'events1@klp48.com')->first();
        $marketingStaff = User::where('email', 'marketing1@klp48.com')->first();

        $yiShyan = Member::where('slug', 'yi-shyan')->first();
        $tiffany = Member::where('slug', 'tiffany')->first();
        $salwa = Member::where('slug', 'salwa')->first();
        $amanda = Member::where('slug', 'amanda')->first();

        $venue = Resource::where('type', 'venue')->first();
        $camera = Resource::where('name', 'Sony A7S III Camera')->first();

        if (!$admin || !$yiShyan || !$tiffany) {
            $this->command->warn('Required users or members not found. Run MemberSeeder and DemoUserSeeder first.');
            return;
        }

        $now = now();

        // Event 1: Performance - Next week
        $event1 = ScheduleEvent::updateOrCreate(
            ['title' => 'Summer Concert 2026'],
            [
                'description' => 'Main summer concert event at KL Live',
                'type' => 'artist_performance',
                'start_datetime' => $now->copy()->addDays(7)->setTime(19, 0),
                'end_datetime' => $now->copy()->addDays(7)->setTime(22, 0),
                'venue' => 'KL Live Performance Hall',
                'status' => 'confirmed',
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $tiffany && $salwa) {
            $event1->members()->sync([$yiShyan->id, $tiffany->id, $salwa->id]);
        }
        if ($venue) {
            $event1->resources()->sync([$venue->id => ['quantity' => 1]]);
        }

        // Event 2: Content Filming - Tomorrow (will conflict with Event 3)
        $event2 = ScheduleEvent::updateOrCreate(
            ['title' => 'Music Video Shoot - Scene 1'],
            [
                'description' => 'First day of music video production',
                'type' => 'content_filming',
                'start_datetime' => $now->copy()->addDay()->setTime(9, 0),
                'end_datetime' => $now->copy()->addDay()->setTime(17, 0),
                'venue' => 'Studio A Recording Room',
                'status' => 'confirmed',
                'created_by' => $marketingStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $amanda) {
            $event2->members()->sync([$yiShyan->id, $amanda->id]);
        }
        if ($camera) {
            $event2->resources()->sync([$camera->id => ['quantity' => 1]]);
        }

        // Event 3: CONFLICT SCENARIO 1 - Artist Double Booking
        // Yi Shyan is already in Event 2 from 9am-5pm
        // This event overlaps from 2pm-6pm
        $event3 = ScheduleEvent::updateOrCreate(
            ['title' => 'Magazine Photo Shoot'],
            [
                'description' => 'Photo shoot for magazine feature (CONFLICTS with filming)',
                'type' => 'artist_appearance',
                'start_datetime' => $now->copy()->addDay()->setTime(14, 0),
                'end_datetime' => $now->copy()->addDay()->setTime(18, 0),
                'venue' => 'External Location - Fashion Studio',
                'status' => 'draft', // Keep as draft due to conflict
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan) {
            $event3->members()->sync([$yiShyan->id]); // Conflict: Yi Shyan double-booked
        }

        // Event 4: Day Off for Tiffany - Day after tomorrow
        $event4 = ScheduleEvent::updateOrCreate(
            ['title' => 'Tiffany - Personal Day Off'],
            [
                'description' => 'Scheduled day off',
                'type' => 'day_off',
                'start_datetime' => $now->copy()->addDays(2)->setTime(0, 0),
                'end_datetime' => $now->copy()->addDays(2)->setTime(23, 59),
                'venue' => null,
                'status' => 'confirmed',
                'created_by' => $admin->id,
            ]
        );
        if ($tiffany) {
            $event4->members()->sync([$tiffany->id]);
        }

        // Event 5: CONFLICT SCENARIO 2 - Day Off Conflict
        // Tiffany has day off on this day, but this practice is scheduled
        $event5 = ScheduleEvent::updateOrCreate(
            ['title' => 'Dance Practice Session'],
            [
                'description' => 'Group dance rehearsal (CONFLICTS with Tiffany day-off)',
                'type' => 'practice_day',
                'start_datetime' => $now->copy()->addDays(2)->setTime(10, 0),
                'end_datetime' => $now->copy()->addDays(2)->setTime(16, 0),
                'venue' => 'Studio A Recording Room',
                'status' => 'draft', // Keep as draft due to conflict
                'created_by' => $marketingStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $tiffany && $salwa) {
            $event5->members()->sync([$yiShyan->id, $tiffany->id, $salwa->id]); // Conflict: Tiffany on day-off
        }

        // Event 6: Social Media Post - Next month
        $event6 = ScheduleEvent::updateOrCreate(
            ['title' => 'Instagram Live Stream'],
            [
                'description' => 'Weekly fan interaction live stream',
                'type' => 'social_media_post',
                'start_datetime' => $now->copy()->addMonth()->setTime(20, 0),
                'end_datetime' => $now->copy()->addMonth()->setTime(21, 0),
                'venue' => 'Online - Instagram',
                'status' => 'confirmed',
                'created_by' => $marketingStaff?->id ?? $admin->id,
            ]
        );
        if ($amanda) {
            $event6->members()->sync([$amanda->id]);
        }

        // Event 7: Staff Meeting
        $event7 = ScheduleEvent::updateOrCreate(
            ['title' => 'Monthly Planning Meeting'],
            [
                'description' => 'Staff coordination and planning session',
                'type' => 'staff_event',
                'start_datetime' => $now->copy()->addDays(3)->setTime(14, 0),
                'end_datetime' => $now->copy()->addDays(3)->setTime(16, 0),
                'venue' => 'Office Conference Room',
                'status' => 'confirmed',
                'created_by' => $admin->id,
            ]
        );
        if ($eventsStaff && $marketingStaff) {
            $event7->staff()->sync([$eventsStaff->id, $marketingStaff->id]);
        }

        $this->command->info('Created 7 schedule events with 2 conflict scenarios:');
        $this->command->info('  - Event 3 conflicts with Event 2 (Yi Shyan double-booked)');
        $this->command->info('  - Event 5 conflicts with Event 4 (Tiffany day-off conflict)');
    }
}
