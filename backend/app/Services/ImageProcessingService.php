<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\ImageInterface;

class ImageProcessingService
{
    // Size presets for different use cases
    private array $sizes = [
        'thumbnail' => ['width' => 150, 'height' => 150],
        'small' => ['width' => 300, 'height' => null],
        'medium' => ['width' => 600, 'height' => null],
        'large' => ['width' => 1200, 'height' => null],
    ];

    private int $quality = 80;
    private int $avifQuality = 65; // AVIF can use lower quality for same visual result
    private string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.media_disk', 'public');
    }

    /**
     * Process and upload an image with multiple sizes in WebP and AVIF formats.
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $directory The storage directory (e.g., 'members', 'news')
     * @param array $generateSizes Which sizes to generate (default: all)
     * @return array Paths to all generated images
     */
    public function process(
        UploadedFile $file,
        string $directory,
        array $generateSizes = ['thumbnail', 'small', 'medium', 'large']
    ): array {
        $baseName = Str::uuid();
        $paths = [];

        try {
            $image = Image::read($file->getRealPath());

            // Store original in both formats
            $paths['original'] = $this->storeAsWebP($image, $directory, $baseName, 'original');
            $paths['original_avif'] = $this->storeAsAvif(clone $image, $directory, $baseName, 'original');

            // Generate requested sizes in both formats
            foreach ($generateSizes as $sizeName) {
                if (!isset($this->sizes[$sizeName])) {
                    continue;
                }

                $size = $this->sizes[$sizeName];

                // WebP version
                $resizedWebp = $this->resize(clone $image, $size['width'], $size['height']);
                $paths[$sizeName] = $this->storeAsWebP($resizedWebp, $directory, $baseName, $sizeName);

                // AVIF version
                $resizedAvif = $this->resize(clone $image, $size['width'], $size['height']);
                $paths["{$sizeName}_avif"] = $this->storeAsAvif($resizedAvif, $directory, $baseName, $sizeName);
            }

            Log::info("Image processed successfully", ['paths' => $paths]);

            return $paths;

        } catch (\Exception $e) {
            Log::error("Image processing failed: " . $e->getMessage());

            // Fallback: store original file without processing
            $fallbackPath = $file->store($directory, $this->disk);
            return ['original' => $fallbackPath];
        }
    }

    /**
     * Process image and return only the main path (for backward compatibility).
     */
    public function processAndGetMain(
        UploadedFile $file,
        string $directory,
        array $generateSizes = ['thumbnail', 'medium']
    ): string {
        $paths = $this->process($file, $directory, $generateSizes);
        return $paths['original'] ?? $paths[array_key_first($paths)];
    }

    /**
     * Resize image maintaining aspect ratio.
     */
    private function resize(ImageInterface $image, ?int $width, ?int $height): ImageInterface
    {
        if ($width && $height) {
            // Cover resize (crop to fit)
            return $image->cover($width, $height);
        }

        if ($width) {
            // Scale down by width, maintain aspect ratio
            return $image->scaleDown(width: $width);
        }

        if ($height) {
            // Scale down by height, maintain aspect ratio
            return $image->scaleDown(height: $height);
        }

        return $image;
    }

    /**
     * Store image as WebP format.
     */
    private function storeAsWebP(
        ImageInterface $image,
        string $directory,
        string $baseName,
        string $suffix
    ): string {
        $filename = "{$baseName}_{$suffix}.webp";
        $path = "{$directory}/{$filename}";

        $encoded = $image->toWebp($this->quality);
        Storage::disk($this->disk)->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Store image as AVIF format.
     */
    private function storeAsAvif(
        ImageInterface $image,
        string $directory,
        string $baseName,
        string $suffix
    ): string {
        $filename = "{$baseName}_{$suffix}.avif";
        $path = "{$directory}/{$filename}";

        $encoded = $image->toAvif($this->avifQuality);
        Storage::disk($this->disk)->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Delete all versions of an image (both WebP and AVIF).
     */
    public function deleteAll(string $originalPath): void
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        // Remove size suffix and extension to get base name
        $baseName = preg_replace('/_(original|thumbnail|small|medium|large)$/', '', $filename);

        $suffixes = ['original', 'thumbnail', 'small', 'medium', 'large'];
        $formats = ['webp', 'avif'];

        foreach ($suffixes as $suffix) {
            foreach ($formats as $format) {
                $path = "{$directory}/{$baseName}_{$suffix}.{$format}";
                if (Storage::disk($this->disk)->exists($path)) {
                    Storage::disk($this->disk)->delete($path);
                }
            }
        }

        // Also try to delete the original path itself (in case it wasn't processed)
        if (Storage::disk($this->disk)->exists($originalPath)) {
            Storage::disk($this->disk)->delete($originalPath);
        }
    }

    /**
     * Get URL for a specific size variant.
     *
     * @param string $originalPath The original stored path
     * @param string $size Size variant: 'original', 'thumbnail', 'small', 'medium', 'large'
     * @param string $format Format: 'webp' or 'avif'
     */
    public function getUrl(string $originalPath, string $size = 'original', string $format = 'webp'): ?string
    {
        if (!$originalPath) {
            return null;
        }

        // If the path already has a size suffix, extract base info
        if (preg_match('/^(.+)_(original|thumbnail|small|medium|large)\.(webp|avif)$/', $originalPath, $matches)) {
            $basePath = $matches[1];
            $variantPath = "{$basePath}_{$size}.{$format}";
        } else {
            // Legacy path without suffix - return as-is
            return Storage::disk($this->disk)->url($originalPath);
        }

        // Check if variant exists, fallback to original WebP
        if (Storage::disk($this->disk)->exists($variantPath)) {
            return Storage::disk($this->disk)->url($variantPath);
        }

        return Storage::disk($this->disk)->url($originalPath);
    }

    /**
     * Get URLs for all sizes in both formats.
     */
    public function getAllUrls(string $originalPath): array
    {
        if (!$originalPath) {
            return $this->emptyUrls();
        }

        // Extract base path
        if (!preg_match('/^(.+)_(original|thumbnail|small|medium|large)\.(webp|avif)$/', $originalPath, $matches)) {
            // Legacy path
            $url = Storage::disk($this->disk)->url($originalPath);
            return [
                'webp' => [
                    'original' => $url,
                    'thumbnail' => $url,
                    'small' => $url,
                    'medium' => $url,
                    'large' => $url,
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

        $basePath = $matches[1];
        $sizes = ['original', 'thumbnail', 'small', 'medium', 'large'];
        $result = ['webp' => [], 'avif' => []];

        foreach ($sizes as $size) {
            foreach (['webp', 'avif'] as $format) {
                $path = "{$basePath}_{$size}.{$format}";
                $result[$format][$size] = Storage::disk($this->disk)->exists($path)
                    ? Storage::disk($this->disk)->url($path)
                    : null;
            }
        }

        return $result;
    }

    private function emptyUrls(): array
    {
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
}
