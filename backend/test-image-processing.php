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
$testPngPath = sys_get_temp_dir() . '/klp48_test.png';
$gdImage = imagecreatetruecolor(800, 600);
imagefill($gdImage, 0, 0, imagecolorallocate($gdImage, 100, 150, 200));
imagepng($gdImage, $testPngPath);
imagedestroy($gdImage);

try {
    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    $image = $manager->decode($testPngPath);
    echo "   ✅ Decode works! Image size: {$image->width()}x{$image->height()}\n";

    $webp = $image->encode(new \Intervention\Image\Encoders\WebpEncoder(80));
    echo "   ✅ WebP encode works! (" . strlen((string) $webp) . " bytes)\n";

    $avif = $image->encode(new \Intervention\Image\Encoders\AvifEncoder(65));
    echo "   ✅ AVIF encode works! (" . strlen((string) $avif) . " bytes)\n\n";
} catch (\Throwable $e) {
    echo "   ❌ Error: " . get_class($e) . ": " . $e->getMessage() . "\n\n";
} finally {
    @unlink($testPngPath);
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
