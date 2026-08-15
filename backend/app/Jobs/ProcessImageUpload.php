<?php

namespace App\Jobs;

use App\Services\ImageProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Resizes/encodes an uploaded image (WebP + AVIF, multiple sizes) and uploads
 * the results to the configured media disk, then writes the resulting path
 * onto the owning model. Runs in the queue so the admin request that
 * triggered the upload doesn't block on encoding + network uploads to R2.
 */
class ProcessImageUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        private readonly string $modelClass,
        private readonly int|string $modelId,
        private readonly string $field,
        private readonly string $tempDiskPath,
        private readonly string $directory,
        private readonly array $sizes,
        private readonly ?string $previousPath,
    ) {
    }

    public function handle(ImageProcessingService $imageService): void
    {
        $absolutePath = Storage::disk('local')->path($this->tempDiskPath);

        if (!Storage::disk('local')->exists($this->tempDiskPath)) {
            Log::error('ProcessImageUpload: temp upload missing', ['path' => $this->tempDiskPath]);
            return;
        }

        $model = $this->modelClass::find($this->modelId);

        if (!$model) {
            // Record was deleted before the job ran — nothing to attach the image to.
            Storage::disk('local')->delete($this->tempDiskPath);
            return;
        }

        $paths = $imageService->processFromPath($absolutePath, $this->directory, $this->sizes);
        $mainPath = $paths['original'] ?? $paths[array_key_first($paths)];

        $model->update([$this->field => $mainPath]);

        if ($this->previousPath && $this->previousPath !== $mainPath) {
            $imageService->deleteAll($this->previousPath);
        }

        Storage::disk('local')->delete($this->tempDiskPath);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('ProcessImageUpload failed', [
            'model'   => $this->modelClass,
            'id'      => $this->modelId,
            'field'   => $this->field,
            'error'   => $exception->getMessage(),
        ]);

        Storage::disk('local')->delete($this->tempDiskPath);
    }
}
