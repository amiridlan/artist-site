<?php

namespace App\Services\SocialMedia;

use App\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    // API endpoint placeholder — Facebook Graph API
    // Requires Facebook App + Page Access Token (long-lived)
    // FACEBOOK_ACCESS_TOKEN=your_token_here
    // Docs: https://developers.facebook.com/docs/graph-api/reference/page
    private string $token;

    public function __construct()
    {
        $this->token = config('social_media.facebook.access_token', '');
    }

    public function fetch(SocialMediaPlatform $platform): ?array
    {
        if (empty($this->token) || empty($platform->channel_id)) {
            Log::info('FacebookService: Access token or page ID not configured — skipping.');
            return null;
        }

        $response = Http::get("https://graph.facebook.com/v19.0/{$platform->channel_id}", [
            'fields'       => 'fan_count,followers_count',
            'access_token' => $this->token,
        ]);

        if (! $response->ok()) {
            Log::error('FacebookService: API error', ['status' => $response->status()]);
            return null;
        }

        $data = $response->json();

        return [
            'followers' => (int) ($data['followers_count'] ?? $data['fan_count'] ?? 0),
        ];
    }
}
