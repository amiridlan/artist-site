<?php

namespace App\Console\Commands;

use App\Models\SocialMediaPlatform;
use App\Models\SocialMediaSnapshot;
use App\Services\SocialMedia\FacebookService;
use App\Services\SocialMedia\InstagramService;
use App\Services\SocialMedia\TikTokService;
use App\Services\SocialMedia\TwitterService;
use App\Services\SocialMedia\YouTubeService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchSocialMediaStats extends Command
{
    protected $signature   = 'social-media:fetch';
    protected $description = 'Fetch latest stats from all active social media platforms';

    private array $services = [];

    public function __construct(
        YouTubeService   $youtube,
        InstagramService $instagram,
        FacebookService  $facebook,
        TikTokService    $tiktok,
        TwitterService   $twitter,
    ) {
        parent::__construct();
        $this->services = [
            'youtube'   => $youtube,
            'instagram' => $instagram,
            'facebook'  => $facebook,
            'tiktok'    => $tiktok,
            'twitter'   => $twitter,
        ];
    }

    public function handle(): int
    {
        $platforms = SocialMediaPlatform::where('is_active', true)->get();

        foreach ($platforms as $platform) {
            $service = $this->services[$platform->platform] ?? null;

            if (! $service) {
                $this->warn("No service found for platform: {$platform->platform}");
                continue;
            }

            $data = $service->fetch($platform);

            if (! $data) {
                $this->line("  Skipped {$platform->display_name} (API not configured)");
                continue;
            }

            SocialMediaSnapshot::create(array_merge($data, [
                'platform_id' => $platform->id,
                'fetched_at'  => Carbon::now(),
            ]));

            $this->info("  Synced {$platform->display_name}: " . number_format($data['followers']) . ' followers');
        }

        $this->info('Social media stats fetch complete.');
        return self::SUCCESS;
    }
}
