<?php

namespace Database\Seeders;

use App\Models\SocialMediaContentItem;
use App\Models\SocialMediaGeoStat;
use App\Models\SocialMediaPlatform;
use App\Models\SocialMediaSnapshot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    public function run(): void
    {
        SocialMediaContentItem::query()->delete();
        SocialMediaGeoStat::query()->delete();
        SocialMediaSnapshot::query()->delete();
        SocialMediaPlatform::query()->delete();

        $now  = Carbon::now();
        $days = 90;

        // ── Geo distribution shared across platforms ──────────────────────────
        $geoBase = [
            ['code' => 'MY', 'name' => 'Malaysia',       'pct' => 42.0],
            ['code' => 'ID', 'name' => 'Indonesia',      'pct' => 24.0],
            ['code' => 'JP', 'name' => 'Japan',          'pct' => 8.5],
            ['code' => 'SG', 'name' => 'Singapore',      'pct' => 7.0],
            ['code' => 'TH', 'name' => 'Thailand',       'pct' => 5.5],
            ['code' => 'PH', 'name' => 'Philippines',    'pct' => 4.0],
            ['code' => 'BN', 'name' => 'Brunei',         'pct' => 2.5],
            ['code' => 'AU', 'name' => 'Australia',      'pct' => 2.0],
            ['code' => 'GB', 'name' => 'United Kingdom', 'pct' => 1.5],
            ['code' => 'US', 'name' => 'United States',  'pct' => 1.0],
            ['code' => 'Other', 'name' => 'Other',       'pct' => 1.5],
        ];

        // ── Platform definitions ──────────────────────────────────────────────
        $platforms = [
            [
                'platform'     => 'youtube',
                'display_name' => 'YouTube',
                'handle'       => '@KLP48Official',
                'channel_id'   => 'YOUR_YOUTUBE_CHANNEL_ID',
                'start'        => ['followers' => 498000, 'views' => 11800000, 'posts' => 152],
                'end'          => ['followers' => 523400, 'views' => 12500000, 'posts' => 156],
            ],
            [
                'platform'     => 'instagram',
                'display_name' => 'Instagram',
                'handle'       => '@klp48official',
                'channel_id'   => 'YOUR_INSTAGRAM_ACCOUNT_ID',
                'start'        => ['followers' => 270000, 'posts' => 305],
                'end'          => ['followers' => 287000, 'posts' => 320],
            ],
            [
                'platform'     => 'tiktok',
                'display_name' => 'TikTok',
                'handle'       => '@klp48official',
                'channel_id'   => 'YOUR_TIKTOK_USER_ID',
                'start'        => ['followers' => 385000, 'posts' => 860, 'likes' => 4200000],
                'end'          => ['followers' => 412000, 'posts' => 890, 'likes' => 4800000],
            ],
            [
                'platform'     => 'facebook',
                'display_name' => 'Facebook',
                'handle'       => 'KLP48Official',
                'channel_id'   => 'YOUR_FACEBOOK_PAGE_ID',
                'start'        => ['followers' => 148000, 'posts' => 440],
                'end'          => ['followers' => 156000, 'posts' => 450],
            ],
            [
                'platform'     => 'twitter',
                'display_name' => 'Twitter / X',
                'handle'       => '@KLP48Official',
                'channel_id'   => 'YOUR_TWITTER_USER_ID',
                'start'        => ['followers' => 93000, 'posts' => 2080],
                'end'          => ['followers' => 98000, 'posts' => 2100],
            ],
        ];

        foreach ($platforms as $config) {
            $platform = SocialMediaPlatform::create([
                'platform'     => $config['platform'],
                'display_name' => $config['display_name'],
                'handle'       => $config['handle'],
                'channel_id'   => $config['channel_id'],
                'is_active'    => true,
            ]);

            // ── 90-day snapshots ──────────────────────────────────────────────
            $start = $config['start'];
            $end   = $config['end'];

            for ($i = 0; $i < $days; $i++) {
                $progress = $i / ($days - 1);
                $isToday  = ($i === $days - 1);

                $snapshot = [
                    'platform_id' => $platform->id,
                    'fetched_at'  => $now->copy()->subDays($days - 1 - $i)->setTime(8, 0, 0),
                ];

                foreach (['followers', 'views', 'posts', 'likes'] as $field) {
                    if (! isset($start[$field])) continue;
                    $base           = (int) round($start[$field] + ($end[$field] - $start[$field]) * $progress);
                    $jitter         = (int) round($base * 0.005 * (mt_rand(-100, 100) / 100));
                    $snapshot[$field] = max(0, $base + $jitter);
                }

                // Store platform-specific extras on the latest snapshot
                if ($isToday) {
                    $snapshot['extra_data'] = $this->buildExtras($config['platform']);
                }

                SocialMediaSnapshot::create($snapshot);
            }

            // ── Geo stats ─────────────────────────────────────────────────────
            $total = $config['end']['followers'];
            foreach ($geoBase as $geo) {
                SocialMediaGeoStat::create([
                    'platform_id'  => $platform->id,
                    'country_code' => $geo['code'],
                    'country_name' => $geo['name'],
                    'value'        => (int) round($total * $geo['pct'] / 100),
                    'percentage'   => $geo['pct'],
                    'fetched_at'   => $now,
                ]);
            }

            // ── Content items ─────────────────────────────────────────────────
            $this->seedContentItems($platform, $now);
        }
    }

    // ── Per-platform extras (traffic sources, demographics, etc.) ─────────────

    private function buildExtras(string $platform): array
    {
        return match ($platform) {
            'youtube'   => [
                'watch_time_hours' => 428600,
                'avg_view_duration' => '4:12',
                'traffic_sources' => [
                    ['label' => 'Browse Features',    'value' => 44],
                    ['label' => 'YouTube Search',     'value' => 29],
                    ['label' => 'Suggested Videos',   'value' => 15],
                    ['label' => 'External / Shares',  'value' => 8],
                    ['label' => 'Direct / Other',     'value' => 4],
                ],
                'subscriber_gain_30d' => $this->dailyBarData(30, 180, 420),
            ],
            'instagram' => [
                'reach_30d'       => 1840000,
                'impressions_30d' => 3200000,
                'demographics' => [
                    ['label' => '18–24', 'female' => 28, 'male' => 14],
                    ['label' => '25–34', 'female' => 22, 'male' => 12],
                    ['label' => '35–44', 'female' => 10, 'male' => 6],
                    ['label' => '45–54', 'female' => 4,  'male' => 2],
                    ['label' => '55+',   'female' => 1,  'male' => 1],
                ],
                'reach_trend' => $this->dailyTrendData(30, 55000, 72000),
            ],
            'facebook'  => [
                'page_views_30d' => 312000,
                'reactions' => [
                    ['label' => 'Like',  'value' => 58],
                    ['label' => 'Love',  'value' => 24],
                    ['label' => 'Haha',  'value' => 8],
                    ['label' => 'Wow',   'value' => 5],
                    ['label' => 'Sad',   'value' => 3],
                    ['label' => 'Angry', 'value' => 2],
                ],
                'demographics' => [
                    ['label' => '18–24', 'female' => 24, 'male' => 12],
                    ['label' => '25–34', 'female' => 20, 'male' => 14],
                    ['label' => '35–44', 'female' => 12, 'male' => 8],
                    ['label' => '45–54', 'female' => 5,  'male' => 3],
                    ['label' => '55+',   'female' => 1,  'male' => 1],
                ],
            ],
            'tiktok'    => [
                'total_likes'     => 4800000,
                'avg_views'       => 48600,
                'activity_hours'  => $this->activityHoursData(),
                'views_trend'     => $this->dailyTrendData(30, 380000, 520000),
            ],
            'twitter'   => [
                'impressions_30d'   => 2140000,
                'avg_impressions'   => 2180,
                'profile_visits_30d'=> 48000,
                'impressions_trend' => $this->dailyTrendData(30, 60000, 82000),
            ],
            default     => [],
        };
    }

    private function dailyBarData(int $days, int $min, int $max): array
    {
        $now  = Carbon::now();
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $data[] = [
                'date'  => $now->copy()->subDays($i)->format('M d'),
                'value' => mt_rand($min, $max),
            ];
        }
        return $data;
    }

    private function dailyTrendData(int $days, int $start, int $end): array
    {
        $now  = Carbon::now();
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $progress = ($days - 1 - $i) / ($days - 1);
            $base     = (int) round($start + ($end - $start) * $progress);
            $jitter   = (int) round($base * 0.04 * (mt_rand(-100, 100) / 100));
            $data[]   = [
                'date'  => $now->copy()->subDays($i)->format('M d'),
                'value' => max(0, $base + $jitter),
            ];
        }
        return $data;
    }

    private function activityHoursData(): array
    {
        $peaks  = [18 => 1.8, 19 => 2.0, 20 => 2.2, 21 => 2.1, 22 => 1.7, 12 => 1.3, 13 => 1.2];
        $result = [];
        for ($h = 0; $h < 24; $h++) {
            $base     = $peaks[$h] ?? (mt_rand(3, 7) / 10);
            $result[] = ['hour' => $h . ':00', 'value' => round($base + (mt_rand(-10, 10) / 100), 2)];
        }
        return $result;
    }

    // ── Content item seeders per platform ─────────────────────────────────────

    private function seedContentItems(SocialMediaPlatform $platform, Carbon $now): void
    {
        match ($platform->platform) {
            'youtube'   => $this->seedYouTube($platform, $now),
            'instagram' => $this->seedInstagram($platform, $now),
            'tiktok'    => $this->seedTikTok($platform, $now),
            'facebook'  => $this->seedFacebook($platform, $now),
            'twitter'   => $this->seedTwitter($platform, $now),
        };
    }

    private function seedYouTube(SocialMediaPlatform $p, Carbon $now): void
    {
        $videos = [
            ['title' => 'KLP48 — "Kau" Official Music Video',                              'days' => 45, 'views' => 1240000, 'likes' => 48200, 'comments' => 3400, 'watch_time' => 5160000],
            ['title' => 'KLP48 Team A 5th Stage "Musim Bunga" | Full Performance',         'days' => 62, 'views' => 456000,  'likes' => 22100, 'comments' => 1800, 'watch_time' => 2736000],
            ['title' => 'KLP48 General Election 2026 — All Member Speeches',               'days' => 78, 'views' => 389000,  'likes' => 19500, 'comments' => 2700, 'watch_time' => 3501000],
            ['title' => 'Behind The Scenes | KLP48 × Red Campaign Photoshoot',             'days' => 30, 'views' => 287000,  'likes' => 15800, 'comments' => 940,  'watch_time' => 1148000],
            ['title' => 'KLP48 Handshake Event Recap | Kuala Lumpur 2026',                 'days' => 55, 'views' => 214000,  'likes' => 12300, 'comments' => 760,  'watch_time' => 856000],
            ['title' => 'KLP48 — "Rintihan Hati" Dance Practice (Full Ver.)',              'days' => 40, 'views' => 198000,  'likes' => 11200, 'comments' => 620,  'watch_time' => 990000],
            ['title' => '2nd Generation Member Introduction | KLP48 2026',                 'days' => 20, 'views' => 176000,  'likes' => 9800,  'comments' => 1100, 'watch_time' => 880000],
            ['title' => 'KLP48 at AKB48 Group Summit Tokyo 2025',                          'days' => 90, 'views' => 158000,  'likes' => 8700,  'comments' => 890,  'watch_time' => 948000],
            ['title' => 'KLP48 Comeback Concert | Full Live Stream',                       'days' => 35, 'views' => 143000,  'likes' => 8100,  'comments' => 1250, 'watch_time' => 1287000],
            ['title' => '"Selamanya" MV Making Film',                                      'days' => 50, 'views' => 128000,  'likes' => 7400,  'comments' => 510,  'watch_time' => 512000],
            ['title' => 'KLP48 Team B — "Malam Minggu" Stage Highlights',                 'days' => 68, 'views' => 112000,  'likes' => 6500,  'comments' => 430,  'watch_time' => 672000],
            ['title' => 'KLP48 Fan Meeting 2026 | Full Event',                             'days' => 25, 'views' => 98000,   'likes' => 5900,  'comments' => 870,  'watch_time' => 980000],
            ['title' => 'Q&A With KLP48 | 500K Subscribers Special',                      'days' => 15, 'views' => 87000,   'likes' => 5200,  'comments' => 640,  'watch_time' => 435000],
            ['title' => 'KLP48 — "Melodi" Acoustic Session',                               'days' => 10, 'views' => 74000,   'likes' => 4800,  'comments' => 380,  'watch_time' => 296000],
            ['title' => 'KLP48 Japan Tour 2025 Vlog',                                      'days' => 5,  'views' => 65000,   'likes' => 4100,  'comments' => 310,  'watch_time' => 390000],
        ];

        foreach ($videos as $idx => $v) {
            SocialMediaContentItem::create([
                'platform_id'  => $p->id,
                'content_id'   => 'yt_' . substr(md5($v['title']), 0, 11),
                'type'         => 'video',
                'title'        => $v['title'],
                'published_at' => $now->copy()->subDays($v['days']),
                'metrics'      => [
                    'views'      => $v['views'],
                    'likes'      => $v['likes'],
                    'comments'   => $v['comments'],
                    'watch_time' => $v['watch_time'],
                ],
                'fetched_at' => $now,
            ]);
        }
    }

    private function seedInstagram(SocialMediaPlatform $p, Carbon $now): void
    {
        $posts = [
            ['caption' => 'KLP48 "Kau" MV Drop — available now on all platforms! 🎬',           'days' => 45, 'likes' => 28400, 'comments' => 1240, 'reach' => 182000, 'impressions' => 310000],
            ['caption' => 'Team A Stage Night 💜 Thank you for coming!',                          'days' => 62, 'likes' => 19800, 'comments' => 890,  'reach' => 124000, 'impressions' => 198000],
            ['caption' => 'General Election 2026 results are in 👑',                              'days' => 78, 'likes' => 24100, 'comments' => 2100, 'reach' => 158000, 'impressions' => 267000],
            ['caption' => 'Behind the scenes of our latest photoshoot ✨',                        'days' => 30, 'likes' => 17600, 'comments' => 680,  'reach' => 108000, 'impressions' => 173000],
            ['caption' => 'Handshake event memories 🤝 Thank you Kuala Lumpur!',                  'days' => 55, 'likes' => 15300, 'comments' => 720,  'reach' => 96000,  'impressions' => 152000],
            ['caption' => '"Rintihan Hati" dance practice drops today!',                          'days' => 40, 'likes' => 13900, 'comments' => 540,  'reach' => 88000,  'impressions' => 138000],
            ['caption' => 'Meet our 2nd Generation members! 💕',                                  'days' => 20, 'likes' => 22400, 'comments' => 1860, 'reach' => 142000, 'impressions' => 228000],
            ['caption' => 'KLP48 at AKB48 Group Summit, Tokyo 🗼',                               'days' => 90, 'likes' => 18700, 'comments' => 980,  'reach' => 118000, 'impressions' => 189000],
            ['caption' => 'Comeback concert recap 🎤 What a night!',                              'days' => 35, 'likes' => 16400, 'comments' => 760,  'reach' => 102000, 'impressions' => 164000],
            ['caption' => '"Selamanya" MV making film is out now!',                               'days' => 50, 'likes' => 12800, 'comments' => 490,  'reach' => 82000,  'impressions' => 131000],
            ['caption' => 'Team B stage highlight reel 💙',                                       'days' => 68, 'likes' => 11200, 'comments' => 420,  'reach' => 72000,  'impressions' => 114000],
            ['caption' => 'Fan meeting 2026 — best day ever with you all 💖',                    'days' => 25, 'likes' => 20100, 'comments' => 1420, 'reach' => 128000, 'impressions' => 205000],
            ['caption' => 'Answering your questions for our 500K subs special!',                  'days' => 15, 'likes' => 9800,  'comments' => 640,  'reach' => 63000,  'impressions' => 100000],
            ['caption' => '"Melodi" acoustic session — listen with headphones 🎧',                'days' => 10, 'likes' => 8700,  'comments' => 380,  'reach' => 56000,  'impressions' => 89000],
            ['caption' => 'Japan tour memories 🌸 Arigato Tokyo!',                               'days' => 5,  'likes' => 14200, 'comments' => 820,  'reach' => 90000,  'impressions' => 144000],
        ];

        foreach ($posts as $post) {
            SocialMediaContentItem::create([
                'platform_id'  => $p->id,
                'content_id'   => 'ig_' . substr(md5($post['caption']), 0, 12),
                'type'         => 'post',
                'title'        => $post['caption'],
                'published_at' => $now->copy()->subDays($post['days']),
                'metrics'      => [
                    'likes'       => $post['likes'],
                    'comments'    => $post['comments'],
                    'reach'       => $post['reach'],
                    'impressions' => $post['impressions'],
                    'engagement_rate' => round(($post['likes'] + $post['comments']) / $post['reach'] * 100, 2),
                ],
                'fetched_at' => $now,
            ]);
        }
    }

    private function seedTikTok(SocialMediaPlatform $p, Carbon $now): void
    {
        $videos = [
            ['caption' => '"Kau" MV teaser — only 15 seconds but it hits different 🔥',           'days' => 44, 'views' => 2840000, 'likes' => 312000, 'comments' => 18400, 'shares' => 84000],
            ['caption' => 'KLP48 dance challenge — try this at home! #KLP48Challenge',             'days' => 38, 'views' => 1920000, 'likes' => 198000, 'comments' => 12600, 'shares' => 56000],
            ['caption' => '2nd Gen members introduction dance 💜',                                 'days' => 19, 'views' => 1540000, 'likes' => 164000, 'comments' => 9800,  'shares' => 42000],
            ['caption' => 'POV: You are at a KLP48 handshake event 🤝',                           'days' => 54, 'views' => 980000,  'likes' => 98000,  'comments' => 6200,  'shares' => 28000],
            ['caption' => '"Rintihan Hati" full dance cover — 2 hours practice!',                  'days' => 39, 'views' => 820000,  'likes' => 82000,  'comments' => 4800,  'shares' => 22000],
            ['caption' => 'KLP48 reaction to our debut year anniversary 🥹',                       'days' => 28, 'views' => 740000,  'likes' => 74000,  'comments' => 5400,  'shares' => 18000],
            ['caption' => 'Fan asked us to recreate AKB48 classic — here you go!',                 'days' => 61, 'views' => 680000,  'likes' => 67000,  'comments' => 4100,  'shares' => 16200],
            ['caption' => 'Tokyo trip diary with KLP48 🗼✈️',                                     'days' => 89, 'views' => 612000,  'likes' => 58000,  'comments' => 3600,  'shares' => 14800],
            ['caption' => 'Comeback concert backstage tour 🎤',                                    'days' => 34, 'views' => 564000,  'likes' => 52000,  'comments' => 3200,  'shares' => 13400],
            ['caption' => 'Members guessing each other\'s hobbies — hilarious results',            'days' => 22, 'views' => 510000,  'likes' => 48000,  'comments' => 4200,  'shares' => 12000],
            ['caption' => 'KLP48 tries Malaysian street food for the first time 🍜',               'days' => 16, 'views' => 468000,  'likes' => 43000,  'comments' => 2800,  'shares' => 11000],
            ['caption' => '"Melodi" acoustic — TikTok version 🎧',                                'days' => 9,  'views' => 420000,  'likes' => 38000,  'comments' => 2200,  'shares' => 9800],
            ['caption' => 'Outfit check: KLP48 stage costumes reveal 👗',                          'days' => 14, 'views' => 386000,  'likes' => 34000,  'comments' => 1900,  'shares' => 8600],
            ['caption' => 'Day in the life of a KLP48 trainee 🎬',                                'days' => 7,  'views' => 348000,  'likes' => 30000,  'comments' => 1700,  'shares' => 7800],
            ['caption' => '"Selamanya" lyrics challenge — how well do you know the song?',         'days' => 3,  'views' => 290000,  'likes' => 24000,  'comments' => 1400,  'shares' => 6400],
        ];

        foreach ($videos as $v) {
            SocialMediaContentItem::create([
                'platform_id'  => $p->id,
                'content_id'   => 'tt_' . substr(md5($v['caption']), 0, 19),
                'type'         => 'video',
                'title'        => $v['caption'],
                'published_at' => $now->copy()->subDays($v['days']),
                'metrics'      => [
                    'views'    => $v['views'],
                    'likes'    => $v['likes'],
                    'comments' => $v['comments'],
                    'shares'   => $v['shares'],
                ],
                'fetched_at' => $now,
            ]);
        }
    }

    private function seedFacebook(SocialMediaPlatform $p, Carbon $now): void
    {
        $posts = [
            ['content' => '🎬 "Kau" Official MV is out now! Watch it on YouTube and let us know your thoughts in the comments.',                                      'days' => 45, 'likes' => 12400, 'comments' => 840, 'shares' => 2800, 'reach' => 94000],
            ['content' => '💜 Team A 5th Stage "Musim Bunga" was an unforgettable night. Thank you to everyone who came!',                                            'days' => 62, 'likes' => 8900,  'comments' => 620, 'shares' => 1800, 'reach' => 68000],
            ['content' => '👑 KLP48 General Election 2026 results revealed! Congratulations to all our Center candidates!',                                           'days' => 78, 'likes' => 10200, 'comments' => 1340,'shares' => 3200, 'reach' => 78000],
            ['content' => '📸 Behind the scenes of our latest photoshoot. A sneak peek at what\'s coming next!',                                                      'days' => 30, 'likes' => 7400,  'comments' => 480, 'shares' => 1400, 'reach' => 57000],
            ['content' => '🤝 Handshake event at Kuala Lumpur — what an incredible turnout! See you at the next one!',                                                'days' => 55, 'likes' => 6800,  'comments' => 540, 'shares' => 1200, 'reach' => 52000],
            ['content' => '🌸 Welcoming our 2nd Generation members! Get to know them on our website.',                                                                'days' => 20, 'likes' => 9600,  'comments' => 980, 'shares' => 2600, 'reach' => 74000],
            ['content' => '🗼 KLP48 represented Malaysia at the AKB48 Group Summit in Tokyo. Proud moment for all of us!',                                            'days' => 90, 'likes' => 8200,  'comments' => 720, 'shares' => 1900, 'reach' => 63000],
            ['content' => '🎤 Last night\'s comeback concert was electric! Thank you for your love and support.',                                                     'days' => 35, 'likes' => 7100,  'comments' => 590, 'shares' => 1600, 'reach' => 55000],
            ['content' => '🎥 "Selamanya" MV making film is live on YouTube! Watch the full behind-the-scenes now.',                                                  'days' => 50, 'likes' => 5800,  'comments' => 380, 'shares' => 1100, 'reach' => 44000],
            ['content' => '💙 Team B "Malam Minggu" Stage Highlights — the energy was unreal! Full show on YouTube.',                                                 'days' => 68, 'likes' => 5200,  'comments' => 320, 'shares' => 980,  'reach' => 40000],
            ['content' => '💖 Fan Meeting 2026 — you made it magical. Photos and videos up now!',                                                                     'days' => 25, 'likes' => 8800,  'comments' => 860, 'shares' => 2100, 'reach' => 68000],
            ['content' => '🎧 Watch our "Melodi" acoustic session on YouTube. This one is extra special.',                                                            'days' => 10, 'likes' => 4800,  'comments' => 260, 'shares' => 860,  'reach' => 37000],
        ];

        foreach ($posts as $post) {
            SocialMediaContentItem::create([
                'platform_id'  => $p->id,
                'content_id'   => 'fb_' . substr(md5($post['content']), 0, 15),
                'type'         => 'post',
                'title'        => $post['content'],
                'published_at' => $now->copy()->subDays($post['days']),
                'metrics'      => [
                    'likes'    => $post['likes'],
                    'comments' => $post['comments'],
                    'shares'   => $post['shares'],
                    'reach'    => $post['reach'],
                ],
                'fetched_at' => $now,
            ]);
        }
    }

    private function seedTwitter(SocialMediaPlatform $p, Carbon $now): void
    {
        $tweets = [
            ['text' => '"Kau" MV is OUT NOW 🎬 Watch it here → youtu.be/klp48kau #KLP48 #Kau',                        'days' => 45, 'impressions' => 142000, 'likes' => 8400, 'retweets' => 3200, 'replies' => 640],
            ['text' => 'General Election 2026 results are in 👑 Full announcement on our YouTube channel! #KLP48Sousenkyo', 'days' => 78, 'impressions' => 118000, 'likes' => 7200, 'retweets' => 2800, 'replies' => 920],
            ['text' => 'Welcoming our 2nd Generation! 💜 Meet them at klp48.my #KLP48 #NewGen',                        'days' => 20, 'impressions' => 98000,  'likes' => 5800, 'retweets' => 2100, 'replies' => 780],
            ['text' => 'Team A "Musim Bunga" stage was a dream 💐 Thank you for the love! #KLP48TeamA',                'days' => 62, 'impressions' => 84000,  'likes' => 4900, 'retweets' => 1800, 'replies' => 420],
            ['text' => 'At the AKB48 Group Summit in Tokyo 🗼 representing Malaysia proud! #AKB48Summit #KLP48',       'days' => 90, 'impressions' => 76000,  'likes' => 4400, 'retweets' => 1600, 'replies' => 380],
            ['text' => 'Comeback concert — SOLD OUT 🎤🔥 Watch the recap on YouTube #KLP48Comeback',                   'days' => 35, 'impressions' => 68000,  'likes' => 3800, 'retweets' => 1400, 'replies' => 310],
            ['text' => 'Fan meeting 2026 was the best day of our lives 💖 Thank you! #KLP48FanMeeting',                'days' => 25, 'impressions' => 62000,  'likes' => 3600, 'retweets' => 1300, 'replies' => 490],
            ['text' => 'Handshake event KL — the queue was incredible! See you next time! 🤝 #KLP48握手会',            'days' => 55, 'impressions' => 54000,  'likes' => 2900, 'retweets' => 1100, 'replies' => 240],
            ['text' => '"Rintihan Hati" dance practice is on YouTube! Can you keep up? 💃 #KLP48Dance',                'days' => 40, 'impressions' => 48000,  'likes' => 2600, 'retweets' => 920,  'replies' => 210],
            ['text' => 'Japan tour 2025 vlog is now live on YouTube 🌸✈️ #KLP48Japan',                                'days' => 5,  'impressions' => 44000,  'likes' => 2400, 'retweets' => 840,  'replies' => 180],
            ['text' => '"Selamanya" MV making film — full video on YouTube 🎬 #KLP48Selamanya',                       'days' => 50, 'impressions' => 40000,  'likes' => 2200, 'retweets' => 780,  'replies' => 162],
            ['text' => '"Melodi" acoustic session out now 🎧 This one is for you #KLP48Melodi',                        'days' => 10, 'impressions' => 36000,  'likes' => 1980, 'retweets' => 680,  'replies' => 148],
            ['text' => 'Photoshoot BTS dropping soon 📸 Stay tuned! #KLP48',                                          'days' => 28, 'impressions' => 32000,  'likes' => 1740, 'retweets' => 580,  'replies' => 134],
            ['text' => 'Team B "Malam Minggu" highlights are up 💙 Watch now on YouTube! #KLP48TeamB',                'days' => 68, 'impressions' => 28000,  'likes' => 1520, 'retweets' => 490,  'replies' => 118],
            ['text' => '500K YouTube subscribers — thank you for every single one of you 🙏 #KLP48',                  'days' => 15, 'impressions' => 24000,  'likes' => 1320, 'retweets' => 420,  'replies' => 108],
        ];

        foreach ($tweets as $tweet) {
            SocialMediaContentItem::create([
                'platform_id'  => $p->id,
                'content_id'   => 'tw_' . substr(md5($tweet['text']), 0, 18),
                'type'         => 'tweet',
                'title'        => $tweet['text'],
                'published_at' => $now->copy()->subDays($tweet['days']),
                'metrics'      => [
                    'impressions' => $tweet['impressions'],
                    'likes'       => $tweet['likes'],
                    'retweets'    => $tweet['retweets'],
                    'replies'     => $tweet['replies'],
                ],
                'fetched_at' => $now,
            ]);
        }
    }
}
