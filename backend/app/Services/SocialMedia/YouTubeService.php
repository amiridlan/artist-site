<?php

namespace App\Services\SocialMedia;

use App\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    // API endpoint placeholder — replace with real key in .env
    // YOUTUBE_API_KEY=your_key_here
    // Docs: https://developers.google.com/youtube/v3/docs/channels/list
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('social_media.youtube.api_key', '');
    }

    public function fetch(SocialMediaPlatform $platform): ?array
    {
        if (empty($this->apiKey) || empty($platform->channel_id)) {
            Log::info('YouTubeService: API key or channel ID not configured — skipping.');
            return null;
        }

        $response = Http::get('https://www.googleapis.com/youtube/v3/channels', [
            'part'  => 'statistics',
            'id'    => $platform->channel_id,
            'key'   => $this->apiKey,
        ]);

        if (! $response->ok()) {
            Log::error('YouTubeService: API error', ['status' => $response->status()]);
            return null;
        }

        $stats = $response->json('items.0.statistics');

        return [
            'followers' => (int) ($stats['subscriberCount'] ?? 0),
            'views'     => (int) ($stats['viewCount'] ?? 0),
            'posts'     => (int) ($stats['videoCount'] ?? 0),
        ];
    }
}
