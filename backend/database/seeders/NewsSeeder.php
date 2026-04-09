<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            ['slug' => 'postponement-kerana-kusuka-release', 'title' => 'Notice: Postponement for the Release of "Kerana Kusuka"', 'excerpt' => 'We regret to inform that the release of "Kerana Kusuka" has been postponed. Further details regarding the new release date will be announced soon.', 'category' => 'news', 'date' => '2026-02-07', 'featured' => true],
            ['slug' => 'conclusion-of-activities-members', 'title' => 'Official Announcement Regarding Conclusion of KLP48 Activities by Cocoa, Suzuha, Haruka, and Yurina', 'excerpt' => 'We would like to inform fans about the conclusion of activities by members Cocoa, Suzuha, Haruka, and Yurina. We thank them for their dedication.', 'category' => 'news', 'date' => '2026-01-31'],
            ['slug' => 'harmful-ai-generated-content-notice', 'title' => 'Notice About Harmful AI-Generated Content', 'excerpt' => 'We are aware of harmful AI-generated content circulating online involving our members. We strongly condemn such actions and urge fans to report any such content.', 'category' => 'news', 'date' => '2026-01-12'],
            ['slug' => 'discord-radio-talk-january', 'title' => 'Discord Radio Talk Information', 'excerpt' => 'Join us for our Discord Radio Talk session! Details on schedule and how to participate are available here.', 'category' => 'fanclub', 'date' => '2026-01-06'],
            ['slug' => 'refund-cocoa-meet-and-greet', 'title' => 'Refund Notification for Cocoa Meet & Greet Session', 'excerpt' => 'Important information regarding refunds for the Cocoa Meet & Greet session. Please check the details for the refund process.', 'category' => 'event', 'date' => '2025-12-29'],
            ['slug' => 'official-statement-year-end', 'title' => 'Official Statement and Year-End Support Office Closure Notice', 'excerpt' => 'An official statement from KLP48 management along with information about the year-end support office closure period.', 'category' => 'news', 'date' => '2025-12-26'],
            ['slug' => 'official-statement-six-members', 'title' => 'Official Statement Regarding Six Members', 'excerpt' => 'An official statement from KLP48 management regarding the status of six members.', 'category' => 'news', 'date' => '2025-12-19'],
            ['slug' => 'super-bash-first-solo-concert', 'title' => 'First Solo Concert "Super Bash" - Ticket Information', 'excerpt' => 'Exciting news! KLP48\'s first solo concert "Super Bash" ticket sales are now open. Get your tickets before they sell out!', 'category' => 'event', 'date' => '2025-11-10', 'featured' => true],
            ['slug' => 'discord-radio-talk-september', 'title' => 'Discord Radio Talk - September Edition', 'excerpt' => 'Details for the September edition of our Discord Radio Talk. Don\'t miss this chance to interact with the members!', 'category' => 'fanclub', 'date' => '2025-09-01'],
            ['slug' => 'green-flash-release', 'title' => '"Green Flash" Now Available', 'excerpt' => 'KLP48\'s cover of the iconic AKB48 hit "Green Flash" is now available on all streaming platforms. Recorded live at our 1st Anniversary Concert.', 'category' => 'release', 'date' => '2025-09-12'],
            ['slug' => 'bloom-concert-announcement', 'title' => 'KLP48 1st Anniversary Concert "BLOOM" at Zepp Kuala Lumpur', 'excerpt' => 'Celebrate our 1st anniversary at Zepp Kuala Lumpur! BLOOM marks a milestone in KLP48\'s journey with special performances and surprises.', 'category' => 'event', 'date' => '2025-08-16', 'featured' => true],
        ];

        foreach ($articles as $article) {
            News::updateOrCreate(['slug' => $article['slug']], $article);
        }
    }
}
