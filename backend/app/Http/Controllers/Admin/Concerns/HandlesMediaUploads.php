<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Jobs\ProcessImageUpload;
use App\Services\ImageProcessingService;
use App\Services\MediaStorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

trait HandlesMediaUploads
{
    private ?MediaStorageService $mediaService = null;
    private ?ImageProcessingService $imageService = null;

    protected function mediaStorage(): MediaStorageService
    {
        if (!$this->mediaService) {
            $this->mediaService = app(MediaStorageService::class);
        }
        return $this->mediaService;
    }

    protected function imageProcessing(): ImageProcessingService
    {
        if (!$this->imageService) {
            $this->imageService = app(ImageProcessingService::class);
        }
        return $this->imageService;
    }

    /**
     * Store media file. Images are automatically processed (resized, converted to WebP).
     */
    protected function storeMedia(UploadedFile $file, string $directory): ?string
    {
        // Check if it's an image
        if ($this->isImage($file)) {
            return $this->imageProcessing()->processAndGetMain($file, $directory);
        }

        // For non-images, use regular storage
        return $this->mediaStorage()->store($file, $directory);
    }

    /**
     * Queue image resizing/encoding + upload instead of doing it inline.
     * Stores the raw upload to fast local disk now (so the request isn't
     * blocked), then a background job produces the WebP/AVIF variants,
     * writes the resulting path onto $model->$field, and cleans up the
     * previous image once the new one is in place.
     */
    protected function queueImageUpload(
        Model $model,
        string $field,
        UploadedFile $file,
        string $directory,
        ?string $previousPath = null,
        array $sizes = ['thumbnail', 'medium'],
    ): void {
        $tempDiskPath = $file->store('pending-uploads', 'local');

        ProcessImageUpload::dispatch(
            $model::class,
            $model->getKey(),
            $field,
            $tempDiskPath,
            $directory,
            $sizes,
            $previousPath,
        );
    }

    /**
     * Delete media and all its variants (for processed images).
     */
    protected function deleteMedia(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Check if it's a processed image (has size suffix)
        if (preg_match('/_(original|thumbnail|small|medium|large)\.webp$/', $path)) {
            $this->imageProcessing()->deleteAll($path);
            return true;
        }

        // Regular file deletion
        return $this->mediaStorage()->delete($path);
    }

    /**
     * Get URL for media, optionally specifying image size.
     */
    protected function mediaUrl(?string $path, string $size = 'original'): ?string
    {
        if (!$path) {
            return null;
        }

        // Check if it's a processed image
        if (preg_match('/_(original|thumbnail|small|medium|large)\.webp$/', $path)) {
            return $this->imageProcessing()->getUrl($path, $size);
        }

        // Regular file URL
        return $this->mediaStorage()->url($path);
    }

    /**
     * Check if file is an image.
     */
    private function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return str_starts_with($mimeType, 'image/');
    }
}
