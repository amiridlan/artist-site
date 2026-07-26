<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageProcessingService;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(web: __DIR__ . '/routes/web.php')
    ->withMiddleware(function ($middleware) {})
    ->withExceptions(function ($exceptions) {})
    ->create();

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Image Processing...\n\n";

// Check extensions
echo "1. PHP Extensions:\n";
echo "   GD: " . (extension_loaded('gd') ? "✅ Enabled" : "❌ Disabled") . "\n";
echo "   Imagick: " . (extension_loaded('imagick') ? "✅ Enabled" : "❌ Disabled") . "\n\n";

// Check Intervention Image
echo "2. Intervention Image:\n";
try {
    $image = \Intervention\Image\Laravel\Facades\Image::read(__DIR__ . '/public/favicon.ico');
    echo "   ✅ Intervention Image works!\n";
    echo "   Image size: {$image->width()}x{$image->height()}\n\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
}

// Check storage disk
echo "3. Storage Configuration:\n";
$disk = config('filesystems.media_disk', 'public');
echo "   Current media disk: {$disk}\n";
echo "   Disk exists: " . (config("filesystems.disks.{$disk}") ? "✅ Yes" : "❌ No") . "\n\n";

// Test formats
echo "4. Supported Formats:\n";
$formats = ['webp', 'avif', 'jpg', 'png'];
foreach ($formats as $format) {
    $supported = gd_info()['WebP Support'] ?? false;
    if ($format === 'avif') {
        $supported = gd_info()['AVIF Support'] ?? false;
    }
    echo "   {$format}: " . ($supported ? "✅" : "⚠️  (check GD version)") . "\n";
}

echo "\n✅ Setup complete! You can now test image uploads.\n";
