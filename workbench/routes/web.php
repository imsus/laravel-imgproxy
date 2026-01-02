<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Workbench Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the WorkbenchServiceProvider which is
| automatically discovered by the package.
|
*/

// Visual test page route
Route::get('/', function () {
    return view('imgproxy-test');
});

Route::prefix('imgproxy-test')->group(function () {
    // Basic URL generation test
    Route::get('/basic', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $processedUrl = imgproxy($imageUrl)
            ->setWidth(400)
            ->setHeight(300)
            ->build();

        return response()->json([
            'original' => $imageUrl,
            'processed' => $processedUrl,
            'test' => 'basic_url_generation',
        ]);
    });

    // Quality and effects test
    Route::get('/effects', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $processedUrl = imgproxy($imageUrl)
            ->setWidth(500)
            ->setHeight(400)
            ->setQuality(85)
            ->setBlur(1.0)
            ->setSharpen(0.5)
            ->build();

        return response()->json([
            'original' => $imageUrl,
            'processed' => $processedUrl,
            'effects_applied' => [
                'quality' => 85,
                'blur' => 1.0,
                'sharpen' => 0.5,
            ],
            'test' => 'effects_and_quality',
        ]);
    });

    // Format conversion test
    Route::get('/formats', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $formats = [
            'jpeg' => imgproxy($imageUrl)->setWidth(300)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::JPEG)->build(),
            'png' => imgproxy($imageUrl)->setWidth(300)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::PNG)->build(),
            'webp' => imgproxy($imageUrl)->setWidth(300)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::WEBP)->build(),
            'avif' => imgproxy($imageUrl)->setWidth(300)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::AVIF)->build(),
        ];

        return response()->json([
            'original' => $imageUrl,
            'formats' => $formats,
            'test' => 'format_conversion',
        ]);
    });

    // Resize types test
    Route::get('/resize', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $resizeTypes = [
            'fit' => imgproxy($imageUrl)->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FIT)->build(),
            'fill' => imgproxy($imageUrl)->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)->build(),
            'force' => imgproxy($imageUrl)->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FORCE)->build(),
            'auto' => imgproxy($imageUrl)->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::AUTO)->build(),
        ];

        return response()->json([
            'original' => $imageUrl,
            'resize_types' => $resizeTypes,
            'test' => 'resize_types',
        ]);
    });

    // Facade vs Helper comparison
    Route::get('/facade-vs-helper', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        // Using Facade
        $facadeUrl = \Imsus\ImgProxy\Facades\ImgProxy::url($imageUrl)
            ->setWidth(400)
            ->setHeight(300)
            ->setQuality(90)
            ->build();

        // Using Helper
        $helperUrl = imgproxy($imageUrl)
            ->setWidth(400)
            ->setHeight(300)
            ->setQuality(90)
            ->build();

        return response()->json([
            'original' => $imageUrl,
            'facade_result' => $facadeUrl,
            'helper_result' => $helperUrl,
            'urls_match' => $facadeUrl === $helperUrl,
            'test' => 'facade_vs_helper',
        ]);
    });

    // Configuration test
    Route::get('/config', function () {
        return response()->json([
            'endpoint' => config('imgproxy.endpoint'),
            'has_key' => ! empty(config('imgproxy.key')),
            'has_salt' => ! empty(config('imgproxy.salt')),
            'default_source_url_mode' => config('imgproxy.default_source_url_mode'),
            'default_output_extension' => config('imgproxy.default_output_extension'),
            'use_short_options' => config('imgproxy.use_short_options'),
            'fallback_url' => config('imgproxy.fallback_url'),
            'test' => 'configuration',
        ]);
    });

    // Error handling test
    Route::get('/error-handling', function () {
        try {
            // This should return the original invalid URL
            $invalidUrl = imgproxy('not-a-valid-url')
                ->setWidth(300)
                ->build();

            // This should throw an exception
            $invalidQuality = null;
            try {
                imgproxy('https://picsum.photos/800/600')
                    ->setQuality(150) // Invalid quality
                    ->build();
            } catch (\InvalidArgumentException $e) {
                $invalidQuality = $e->getMessage();
            }

            return response()->json([
                'invalid_url_handling' => $invalidUrl,
                'invalid_quality_error' => $invalidQuality,
                'test' => 'error_handling',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'test' => 'error_handling',
            ], 500);
        }
    });

    // Performance test
    Route::get('/performance', function () {
        $imageUrl = 'https://picsum.photos/800/600';
        $start = microtime(true);

        // Generate 100 URLs to test performance
        $urls = [];
        for ($i = 0; $i < 100; $i++) {
            $urls[] = imgproxy($imageUrl)
                ->setWidth(300 + $i)
                ->setHeight(200 + $i)
                ->setQuality(80 + ($i % 20))
                ->build();
        }

        $duration = microtime(true) - $start;

        return response()->json([
            'urls_generated' => count($urls),
            'duration_seconds' => round($duration, 4),
            'urls_per_second' => round(count($urls) / $duration, 2),
            'sample_urls' => array_slice($urls, 0, 3),
            'test' => 'performance',
        ]);
    });

    // Core processing test
    Route::get('/core-processing', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $processedUrl = imgproxy($imageUrl)
            ->setWidth(500)
            ->setHeight(400)
            ->padding(20)
            ->background('FF5733')
            ->autoRotate(true)
            ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_90)
            ->stripMetadata(true)
            ->trim(15)
            ->pixelate(10)
            ->build();

        return response()->json([
            'original' => $imageUrl,
            'processed' => $processedUrl,
            'processing_applied' => [
                'padding' => 20,
                'background' => 'FF5733',
                'auto_rotate' => true,
                'rotate' => 90,
                'strip_metadata' => true,
                'trim' => 15,
                'pixelate' => 10,
            ],
            'test' => 'core_processing',
        ]);
    });

    // Watermark test
    Route::get('/watermark', function () {
        $imageUrl = 'https://picsum.photos/800/600';

        $processedUrl = imgproxy($imageUrl)
            ->setWidth(500)
            ->setHeight(400)
            ->watermark(0.7, \Imsus\ImgProxy\Enums\Gravity::SOUTH_EAST, 0, 0, 0.25)
            ->build();

        return response()->json([
            'original' => $imageUrl,
            'processed' => $processedUrl,
            'watermark_applied' => [
                'opacity' => 0.7,
                'position' => 'south_east',
                'scale' => 0.25,
            ],
            'test' => 'watermark',
            'note' => 'Custom watermark URL requires ImgProxy Pro',
        ]);
    });

    // Test index with all available tests
    Route::get('/', function () {
        return response()->json([
            'message' => 'ImgProxy Laravel Package Test Suite',
            'available_tests' => [
                '/imgproxy-test/basic' => 'Basic URL generation',
                '/imgproxy-test/effects' => 'Quality and effects testing',
                '/imgproxy-test/formats' => 'Format conversion testing',
                '/imgproxy-test/resize' => 'Resize types testing',
                '/imgproxy-test/facade-vs-helper' => 'Facade vs Helper comparison',
                '/imgproxy-test/config' => 'Configuration testing',
                '/imgproxy-test/error-handling' => 'Error handling testing',
                '/imgproxy-test/performance' => 'Performance testing',
                '/imgproxy-test/core-processing' => 'Core processing options',
                '/imgproxy-test/watermark' => 'Watermarking options',
            ],
            'usage' => 'Visit any of the test endpoints to see ImgProxy in action',
            'visual_tests' => 'Visit /imgproxy-visual-test for browser-based visual testing',
        ]);
    });
});
