<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles protected media storage for Fan Club content
 * - Uses r2-fanclub disk (private bucket)
 * - Generates temporary signed URLs for authorized access
 */
class FanClubMediaService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.fanclub_disk', 'public');
    }

    /**
     * Store a file in the protected fan club bucket
     *
     * @param UploadedFile $file
     * @param string $directory Directory within the bucket (e.g., 'exclusive-photos', 'videos')
     * @return string|false The stored file path or false on failure
     */
    public function store(UploadedFile $file, string $directory = 'fanclub'): string|false
    {
        try {
            return $file->store($directory, $this->disk);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Delete a file from the protected bucket
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->delete($path);
            }
            return false;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Generate a temporary signed URL for protected content
     * Only users with active subscriptions should be able to access these URLs
     *
     * @param string $path File path in the bucket
     * @param int $expirationMinutes How long the URL should be valid (default: 60 minutes)
     * @return string Temporary signed URL
     */
    public function getTemporaryUrl(string $path, int $expirationMinutes = 60): string
    {
        try {
            return Storage::disk($this->disk)->temporaryUrl(
                $path,
                now()->addMinutes($expirationMinutes)
            );
        } catch (\Exception $e) {
            report($e);
            // Return a placeholder or throw depending on your needs
            throw new \RuntimeException("Unable to generate signed URL for protected content");
        }
    }

    /**
     * Check if a file exists in the protected bucket
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->exists($path);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Get file size
     *
     * @param string $path
     * @return int File size in bytes
     */
    public function size(string $path): int
    {
        try {
            return Storage::disk($this->disk)->size($path);
        } catch (\Exception $e) {
            report($e);
            return 0;
        }
    }

    /**
     * List all files in a directory
     *
     * @param string $directory
     * @return array
     */
    public function files(string $directory = ''): array
    {
        try {
            return Storage::disk($this->disk)->files($directory);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }
}
