<?php

namespace App\Services\SocialMedia;

use App\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwitterService
{
    // API endpoint placeholder — X (Twitter) API v2
    // Requires X Developer account + Bearer Token
    // Free tier may have limited follower data; Basic tier ($100/mo) recommended
    // TWITTER_BEARER_TOKEN=your_token_here
    // Docs: https://developer.twitter.com/en/docs/twitter-api/users/lookup/api-reference
    private string $bearerToken;

    public function __construct()
    {
        $this->bearerToken = config('social_media.twitter.bearer_token', '');
    }

    public function fetch(SocialMediaPlatform $platform): ?array
    {
        if (empty($this->bearerToken) || empty($platform->channel_id)) {
            Log::info('TwitterService: Bearer token or user ID not configured — skipping.');
            return null;
        }

        $response = Http::withToken($this->bearerToken)
            ->get("https://api.twitter.com/2/users/{$platform->channel_id}", [
                'user.fields' => 'public_metrics',
            ]);

        if (! $response->ok()) {
            Log::error('TwitterService: API error', ['status' => $response->status()]);
            return null;
        }

        $metrics = $response->json('data.public_metrics');

        return [
            'followers' => (int) ($metrics['followers_count'] ?? 0),
            'posts'     => (int) ($metrics['tweet_count'] ?? 0),
        ];
    }
}
