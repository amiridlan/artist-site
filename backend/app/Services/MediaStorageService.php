<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaStorageService
{
    private string $primaryDisk;
    private ?string $fallbackDisk;
    private bool $mirrorUploads;

    public function __construct()
    {
        $this->primaryDisk = config('filesystems.media_disk', 'r2');
        $this->fallbackDisk = config('filesystems.media_fallback_disk');
        $this->mirrorUploads = config('filesystems.media_mirror', false);
    }

    /**
     * Store an uploaded file with fallback support.
     */
    public function store(UploadedFile $file, string $directory): ?string
    {
        $path = null;

        // Try primary disk (R2)
        try {
            $path = $file->store($directory, $this->primaryDisk);
            Log::info("Media uploaded to primary disk [{$this->primaryDisk}]: {$path}");
        } catch (\Exception $e) {
            Log::error("Primary disk [{$this->primaryDisk}] upload failed: " . $e->getMessage());

            // Fallback to secondary disk (S3)
            if ($this->fallbackDisk) {
                try {
                    $path = $file->store($directory, $this->fallbackDisk);
                    Log::warning("Media uploaded to fallback disk [{$this->fallbackDisk}]: {$path}");
                } catch (\Exception $e2) {
                    Log::critical("Fallback disk [{$this->fallbackDisk}] upload also failed: " . $e2->getMessage());
                    return null;
                }
            }
        }

        // Mirror to fallback disk if enabled and primary succeeded
        if ($path && $this->mirrorUploads && $this->fallbackDisk) {
            $this->mirrorToFallback($path);
        }

        return $path;
    }

    /**
     * Delete a file from all configured disks.
     */
    public function delete(string $path): bool
    {
        $deleted = false;

        try {
            if (Storage::disk($this->primaryDisk)->exists($path)) {
                Storage::disk($this->primaryDisk)->delete($path);
                $deleted = true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to delete from primary disk: " . $e->getMessage());
        }

        if ($this->fallbackDisk) {
            try {
                if (Storage::disk($this->fallbackDisk)->exists($path)) {
                    Storage::disk($this->fallbackDisk)->delete($path);
                    $deleted = true;
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete from fallback disk: " . $e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * Get the public URL for a file, trying primary first then fallback.
     */
    public function url(string $path): ?string
    {
        // Try primary disk first
        try {
            if (Storage::disk($this->primaryDisk)->exists($path)) {
                return Storage::disk($this->primaryDisk)->url($path);
            }
        } catch (\Exception $e) {
            Log::warning("Primary disk URL failed: " . $e->getMessage());
        }

        // Try fallback disk
        if ($this->fallbackDisk) {
            try {
                if (Storage::disk($this->fallbackDisk)->exists($path)) {
                    return Storage::disk($this->fallbackDisk)->url($path);
                }
            } catch (\Exception $e) {
                Log::warning("Fallback disk URL failed: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Mirror a file to the fallback disk.
     */
    private function mirrorToFallback(string $path): void
    {
        try {
            $contents = Storage::disk($this->primaryDisk)->get($path);
            Storage::disk($this->fallbackDisk)->put($path, $contents);
            Log::info("Mirrored {$path} to fallback disk [{$this->fallbackDisk}]");
        } catch (\Exception $e) {
            Log::error("Mirror to fallback failed: " . $e->getMessage());
        }
    }

    /**
     * Get the primary disk name.
     */
    public function getPrimaryDisk(): string
    {
        return $this->primaryDisk;
    }

    /**
     * Get the fallback disk name.
     */
    public function getFallbackDisk(): ?string
    {
        return $this->fallbackDisk;
    }
}
