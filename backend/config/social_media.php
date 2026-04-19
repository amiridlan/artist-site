<?php

return [

    'youtube' => [
        // Google Cloud Console → YouTube Data API v3 → API Key
        'api_key' => env('YOUTUBE_API_KEY', ''),
    ],

    'instagram' => [
        // Facebook Developers → Instagram Graph API → Long-lived Page Access Token
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN', ''),
    ],

    'facebook' => [
        // Facebook Developers → Graph API → Page Access Token
        'access_token' => env('FACEBOOK_ACCESS_TOKEN', ''),
    ],

    'tiktok' => [
        // TikTok Developers → Research API (requires approval)
        'client_key'    => env('TIKTOK_CLIENT_KEY', ''),
        'client_secret' => env('TIKTOK_CLIENT_SECRET', ''),
    ],

    'twitter' => [
        // X Developer Portal → Project App → Bearer Token
        'bearer_token' => env('TWITTER_BEARER_TOKEN', ''),
    ],

];
