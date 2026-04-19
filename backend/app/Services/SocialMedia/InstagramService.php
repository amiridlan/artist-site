<?php

namespace App\Services\SocialMedia;

use App\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramService
{
    // API endpoint placeholder — Instagram Graph API
    // Requires Facebook App + Instagram Business Account + long-lived Page Access Token
    // INSTAGRAM_ACCESS_TOKEN=your_token_here
    // INSTAGRAM_ACCOUNT_ID=your_account_id_here
    // Docs: https://developers.facebook.com/docs/instagram-api/reference/ig-user
    private string $token;

    public function __construct()
    {
        $this->token = config('social_media.instagram.access_token', '');
    }

    public function fetch(SocialMediaPlatform $platform): ?array
    {
        if (empty($this->token) || empty($platform->channel_id)) {
            Log::info('InstagramService: Access token or account ID not configured — skipping.');
            return null;
        }

        $response = Http::get("https://graph.facebook.com/v19.0/{$platform->channel_id}", [
            'fields'       => 'followers_count,media_count',
            'access_token' => $this->token,
        ]);

        if (! $response->ok()) {
            Log::error('InstagramService: API error', ['status' => $response->status()]);
            return null;
        }

        $data = $response->json();

        return [
            'followers' => (int) ($data['followers_count'] ?? 0),
            'posts'     => (int) ($data['media_count'] ?? 0),
        ];
    }
}
