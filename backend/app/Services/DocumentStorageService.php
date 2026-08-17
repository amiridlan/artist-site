<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles private storage for compliance documents (signed contracts, IDs,
 * guardian-consent forms).
 * - Uses documents_disk (private by default, even in dev)
 * - Generates short-lived signed URLs for authorized access
 */
class DocumentStorageService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.documents_disk', 'local');
    }

    /**
     * Store a file in the private documents bucket.
     */
    public function store(UploadedFile $file, string $directory = 'documents'): string|false
    {
        try {
            return $file->store($directory, $this->disk);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Delete a file from the private bucket.
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
     * Generate a short-lived signed URL for a document.
     */
    public function getTemporaryUrl(string $path, int $expirationMinutes = 15): string
    {
        try {
            return Storage::disk($this->disk)->temporaryUrl(
                $path,
                now()->addMinutes($expirationMinutes)
            );
        } catch (\Exception $e) {
            report($e);
            throw new \RuntimeException('Unable to generate signed URL for document');
        }
    }

    public function exists(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->exists($path);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
