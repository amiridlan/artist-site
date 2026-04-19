<?php

namespace App\Services\SocialMedia;

use App\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Log;

class TikTokService
{
    // API endpoint placeholder — TikTok Research API
    // Requires approved TikTok Developer account + Research API access
    // TIKTOK_CLIENT_KEY=your_key_here
    // TIKTOK_CLIENT_SECRET=your_secret_here
    // Docs: https://developers.tiktok.com/products/research-api/
    //
    // NOTE: TikTok Research API approval can take several weeks.
    // Until approved, stats are managed via manual input in the admin panel.

    public function fetch(SocialMediaPlatform $platform): ?array
    {
        Log::info('TikTokService: API not yet configured — using manual/seeded data.');
        return null;
    }
}
