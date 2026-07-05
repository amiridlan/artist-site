<?php

use App\Services\ImageProcessingService;
use App\Services\MediaStorageService;

if (!function_exists('media_url')) {
    /**
     * Get the URL for a media file.
     *
     * @param string|null $path The stored file path
     * @param string $size For images: 'original', 'thumbnail', 'small', 'medium', 'large'
     * @param string $format For images: 'webp' or 'avif'
     */
    function media_url(?string $path, string $size = 'original', string $format = 'webp'): ?string
    {
        if (!$path) {
            return null;
        }

        // Check if it's a processed image
        if (preg_match('/_(original|thumbnail|small|medium|large)\.(webp|avif)$/', $path)) {
            return app(ImageProcessingService::class)->getUrl($path, $size, $format);
        }

        // Regular file - use MediaStorageService
        return app(MediaStorageService::class)->url($path);
    }
}

if (!function_exists('media_urls')) {
    /**
     * Get URLs for all available sizes and formats of an image.
     *
     * @param string|null $path The stored file path
     * @return array URLs organized by format and size
     */
    function media_urls(?string $path): array
    {
        if (!$path) {
            return [
                'webp' => [
                    'original' => null,
                    'thumbnail' => null,
                    'small' => null,
                    'medium' => null,
                    'large' => null,
                ],
                'avif' => [
                    'original' => null,
                    'thumbnail' => null,
                    'small' => null,
                    'medium' => null,
                    'large' => null,
                ],
            ];
        }

        return app(ImageProcessingService::class)->getAllUrls($path);
    }
}
