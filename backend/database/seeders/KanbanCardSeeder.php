<?php

namespace Database\Seeders;

use App\Models\KanbanCard;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class KanbanCardSeeder extends Seeder
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

        if (!$admin) {
            $this->command->warn('Admin user not found. Run DemoUserSeeder first.');
            return;
        }

        $now = now();

        // Card 1: Backlog - New performance idea
        $card1 = KanbanCard::updateOrCreate(
            ['title' => 'Autumn Fan Meeting 2026'],
            [
                'description' => 'Plan autumn fan meeting event with Q&A and performance segments',
                'type' => 'artist_performance',
                'stage' => 'backlog',
                'due_date' => $now->copy()->addMonths(3),
                'position' => 1,
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $tiffany) {
            $card1->members()->sync([$yiShyan->id, $tiffany->id]);
        }

        // Card 2: Backlog - Content idea
        $card2 = KanbanCard::updateOrCreate(
            ['title' => 'Behind-the-Scenes Vlog Series'],
            [
                'description' => 'Weekly vlog content for YouTube channel',
                'type' => 'content_filming',
                'stage' => 'backlog',
                'due_date' => $now->copy()->addMonth(),
                'position' => 2,
                'created_by' => $marketingStaff?->id ?? $admin->id,
            ]
        );
        if ($amanda) {
            $card2->members()->sync([$amanda->id]);
        }

        // Card 3: Planning - Radio appearance
        $card3 = KanbanCard::updateOrCreate(
            ['title' => 'Radio Interview - Hot FM'],
            [
                'description' => 'Promote new single on morning radio show',
                'type' => 'artist_appearance',
                'stage' => 'planning',
                'due_date' => $now->copy()->addWeeks(2),
                'position' => 1,
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $salwa) {
            $card3->members()->sync([$yiShyan->id, $salwa->id]);
        }

        // Card 4: Planning - Social media campaign
        $card4 = KanbanCard::updateOrCreate(
            ['title' => 'TikTok Dance Challenge'],
            [
                'description' => 'Launch viral dance challenge for new song',
                'type' => 'social_media_post',
                'stage' => 'planning',
                'due_date' => $now->copy()->addDays(10),
                'position' => 2,
                'created_by' => $marketingStaff?->id ?? $admin->id,
            ]
        );
        if ($tiffany && $amanda) {
            $card4->members()->sync([$tiffany->id, $amanda->id]);
        }

        // Card 5: Confirmed - Already has schedule event (from ScheduleEventSeeder)
        $card5 = KanbanCard::updateOrCreate(
            ['title' => 'Summer Concert 2026'],
            [
                'description' => 'Main summer concert event (already scheduled)',
                'type' => 'artist_performance',
                'stage' => 'confirmed',
                'due_date' => $now->copy()->addDays(7),
                'position' => 1,
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $tiffany && $salwa) {
            $card5->members()->sync([$yiShyan->id, $tiffany->id, $salwa->id]);
        }

        // Card 6: Completed - Past event
        $card6 = KanbanCard::updateOrCreate(
            ['title' => 'Spring Showcase Concert'],
            [
                'description' => 'Completed spring event (archived)',
                'type' => 'artist_performance',
                'stage' => 'completed',
                'due_date' => $now->copy()->subMonth(),
                'position' => 1,
                'created_by' => $eventsStaff?->id ?? $admin->id,
            ]
        );
        if ($yiShyan && $amanda) {
            $card6->members()->sync([$yiShyan->id, $amanda->id]);
        }

        $this->command->info('Created 6 kanban cards across all stages');
    }
}
