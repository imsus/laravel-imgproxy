# Laravel integration for ImgProxy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/imsus/laravel-imgproxy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/run-tests.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/imsus/laravel-imgproxy/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)

A comprehensive Laravel package for [ImgProxy](https://imgproxy.net/) integration. Generate optimized, signed image URLs with fluent API including resizing, quality control, visual effects, and advanced processing options.

## Features

-   🚀 **Fluent API** - Clean, chainable method syntax
-   🔒 **Secure URLs** - HMAC-SHA256 signed URLs with hex key/salt validation
-   🎨 **Visual Effects** - Blur, sharpen, brightness, contrast, saturation adjustments
-   ⚡ **Quality Control** - Fine-tune compression for JPEG, WebP, AVIF formats
-   📐 **Flexible Resizing** - Multiple resize modes with DPR support
-   🔧 **Laravel Integration** - Service provider, facade, and helper function
-   ✅ **Type Safe** - PHP 8.2+ enums and comprehensive validation
-   🧪 **Well Tested** - 71+ tests with workbench integration & visual testing

## Installation

You can install the package via composer:

```bash
composer require imsus/laravel-imgproxy
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="imgproxy-config"
```

This is the contents of the published config file:

```php
return [
    'endpoint' => env('IMGPROXY_ENDPOINT', 'http://localhost:8080'),
    'key' => env('IMGPROXY_KEY'),
    'salt' => env('IMGPROXY_SALT'),
    'default_source_url_mode' => env('IMGPROXY_DEFAULT_SOURCE_URL_MODE', 'encoded'),
    'default_output_extension' => env('IMGPROXY_DEFAULT_OUTPUT_EXTENSION', 'jpeg'),
];
```

## Configuration

You can configure the package by updating the values in your `.env` file:

```dotenv
IMGPROXY_ENDPOINT=http://localhost:8080
IMGPROXY_KEY=your_hex_key_here
IMGPROXY_SALT=your_hex_salt_here
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=jpeg
```

> [!NOTE]
> The `key` and `salt` are required only if you want to generate signed URLs. If you don't want to generate signed URLs, you can leave them empty.

> [!CAUTION]
> The `key` and `salt` should be in hex-encoded format. Generate them using: `openssl rand -hex 32`

### Configuration Options

| Option                     | Description               | Default                 | Options                             |
| -------------------------- | ------------------------- | ----------------------- | ----------------------------------- |
| `endpoint`                 | ImgProxy server URL       | `http://localhost:8080` | Any valid URL                       |
| `key`                      | Hex-encoded signing key   | `null`                  | 64-char hex string                  |
| `salt`                     | Hex-encoded signing salt  | `null`                  | 64-char hex string                  |
| `default_source_url_mode`  | How to encode source URLs | `encoded`               | `encoded`, `plain`                  |
| `default_output_extension` | Default output format     | `jpeg`                  | `jpeg`, `png`, `webp`, `avif`, etc. |

## Usage

### Basic Usage

```php
use Imsus\ImgProxy\Facades\ImgProxy;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

// Generate URL using Facade
$url = ImgProxy::url('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();

// Generate URL using helper function
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();
```

### Resizing & Formatting

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(400)
    ->setHeight(300)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setDpr(2)  // High DPI displays
    ->build();
```

### Quality Control

```php
// Optimize for different use cases
$thumbnail = imgproxy($image)
    ->setWidth(150)
    ->setHeight(150)
    ->setQuality(70)  // Lower quality for thumbnails
    ->build();

$hero = imgproxy($image)
    ->setWidth(1200)
    ->setHeight(600)
    ->setQuality(90)  // Higher quality for hero images
    ->build();
```

### Visual Effects

```php
$url = imgproxy('https://example.com/photo.jpg')
    ->setWidth(500)
    ->setHeight(300)
    ->setBlur(2.0)          // Blur effect
    ->setSharpen(1.5)       // Sharpen details
    ->setBrightness(20)     // Increase brightness
    ->setContrast(1.2)      // Enhance contrast
    ->setSaturation(1.1)    // Boost saturation
    ->build();
```

### Advanced Processing

```php
use Imsus\ImgProxy\Enums\SourceUrlMode;

// Plain URL mode for debugging
$url = imgproxy('https://example.com/image.jpg')
    ->setMode(SourceUrlMode::PLAIN)
    ->setWidth(300)
    ->setHeight(200)
    ->build();

// Custom processing options
$url = imgproxy('https://example.com/image.jpg')
    ->setProcessing('rs:fill:400:300:1/rt:fit/q:85/bl:2.0')
    ->build();
```

### Method Chaining Examples

```php
// Complete image optimization pipeline
$optimizedUrl = imgproxy($originalImage)
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->setSharpen(1.0)
    ->setDpr(2)
    ->build();

// Portrait enhancement
$portraitUrl = imgproxy($portrait)
    ->setWidth(400)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setBrightness(10)
    ->setContrast(1.1)
    ->setSaturation(1.05)
    ->setQuality(90)
    ->build();
```

## API Reference

### Available Methods

| Method                               | Parameters        | Description                     |
| ------------------------------------ | ----------------- | ------------------------------- |
| `url(string $url)`                   | Image URL         | Set the source image URL        |
| `setWidth(int $width)`               | Width in pixels   | Set image width                 |
| `setHeight(int $height)`             | Height in pixels  | Set image height                |
| `setResizeType(ResizeType $type)`    | Resize mode       | Set how image should be resized |
| `setExtension(OutputExtension $ext)` | Output format     | Set output image format         |
| `setDpr(int $dpr)`                   | 1-8               | Set device pixel ratio          |
| `setQuality(int $quality)`           | 0-100             | Set compression quality         |
| `setBlur(float $sigma)`              | ≥0.0              | Apply blur effect               |
| `setSharpen(float $sigma)`           | ≥0.0              | Apply sharpen effect            |
| `setBrightness(int $brightness)`     | -255 to 255       | Adjust brightness               |
| `setContrast(float $contrast)`       | ≥0.0              | Adjust contrast                 |
| `setSaturation(float $saturation)`   | ≥0.0              | Adjust saturation               |
| `setMode(SourceUrlMode $mode)`       | `encoded`/`plain` | Set URL encoding mode           |
| `setProcessing(string $options)`     | Processing string | Custom processing options       |
| `build()`                            | -                 | Generate final URL              |

### Enums

#### ResizeType

-   `ResizeType::FIT` - Resize keeping aspect ratio to fit dimensions
-   `ResizeType::FILL` - Resize keeping aspect ratio to fill dimensions (crops overflow)
-   `ResizeType::FILL_DOWN` - Same as fill, but maintains requested aspect ratio for smaller images
-   `ResizeType::FORCE` - Resize without keeping aspect ratio
-   `ResizeType::AUTO` - Automatically choose between fit/fill based on orientation

#### OutputExtension

-   `OutputExtension::JPEG` - JPEG format
-   `OutputExtension::PNG` - PNG format
-   `OutputExtension::WEBP` - WebP format
-   `OutputExtension::AVIF` - AVIF format
-   `OutputExtension::GIF` - GIF format
-   `OutputExtension::ICO` - ICO format
-   `OutputExtension::SVG` - SVG format
-   `OutputExtension::HEIC` - HEIC format
-   `OutputExtension::BMP` - BMP format
-   `OutputExtension::TIFF` - TIFF format

#### SourceUrlMode

-   `SourceUrlMode::ENCODED` - Base64 encode source URL (default)
-   `SourceUrlMode::PLAIN` - Use plain text URL

## Error Handling

The package includes comprehensive validation and will throw `InvalidArgumentException` for invalid parameters:

```php
try {
    $url = imgproxy('invalid-url')
        ->setQuality(150)  // Invalid: > 100
        ->build();
} catch (InvalidArgumentException $e) {
    // Handle validation error
    echo $e->getMessage(); // "Quality must be between 0 and 100"
}
```

For invalid URLs, the package gracefully returns the original URL instead of throwing an exception.

## Troubleshooting

### Common Issues

**Problem**: Getting "insecure" URLs instead of signed URLs

```
http://localhost:8080/insecure/width:300/height:200/...
```

**Solution**: Ensure `IMGPROXY_KEY` and `IMGPROXY_SALT` are set in your `.env` file with valid hex values.

**Problem**: Invalid hex key/salt errors

```
InvalidArgumentException: The key must be a hex-encoded string.
```

**Solution**: Generate proper hex keys:

```bash
# Generate 32-byte hex key and salt
openssl rand -hex 32
```

**Problem**: Images not loading/404 errors
**Solutions**:

-   Verify ImgProxy server is running at the configured endpoint
-   Check source image URLs are accessible
-   Ensure ImgProxy server can reach source URLs (firewall/network issues)

**Problem**: Poor image quality  
**Solutions**:

-   Increase quality setting: `->setQuality(90)`
-   Use appropriate output format: `->setExtension(OutputExtension::WEBP)`
-   Avoid excessive sharpening: `->setSharpen(1.0)` instead of higher values

### Debug Mode

Enable plain URL mode for debugging:

```php
$debugUrl = imgproxy('https://example.com/image.jpg')
    ->setMode(SourceUrlMode::PLAIN)
    ->setWidth(300)
    ->build();

echo $debugUrl;
// Output: http://localhost:8080/signature/width:300/plain/https://example.com/image.jpg@jpg
```

## Advanced Usage & Patterns

### Performance Optimization

Choose optimal quality settings based on image use case:

```php
// Thumbnails - prioritize small file size
$thumbnail = imgproxy($image)
    ->setWidth(150)
    ->setHeight(150)
    ->setQuality(60)
    ->setExtension(OutputExtension::WEBP)
    ->build();

// Hero images - balance quality and size
$hero = imgproxy($image)
    ->setWidth(1920)
    ->setHeight(1080)
    ->setQuality(85)
    ->setExtension(OutputExtension::WEBP)
    ->build();

// Product images - prioritize quality
$product = imgproxy($image)
    ->setWidth(800)
    ->setHeight(600)
    ->setQuality(95)
    ->setSharpen(0.5)
    ->build();
```

### Format Selection Strategy

```php
// Modern browsers - use AVIF for best compression
$avifUrl = imgproxy($image)
    ->setExtension(OutputExtension::AVIF)
    ->setQuality(75)  // AVIF allows lower quality with better visual results
    ->build();

// Fallback for older browsers - use WebP
$webpUrl = imgproxy($image)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->build();

// Universal fallback - use JPEG
$jpegUrl = imgproxy($image)
    ->setExtension(OutputExtension::JPEG)
    ->setQuality(90)
    ->build();
```

### Laravel Integration

#### Blade Directives

Create custom Blade directives for common use cases:

```php
// In AppServiceProvider::boot()
use Illuminate\Support\Facades\Blade;

Blade::directive('imgproxy', function ($expression) {
    return "<?php echo imgproxy($expression)->build(); ?>";
});

Blade::directive('avatar', function ($expression) {
    return "<?php echo imgproxy($expression)->setWidth(150)->setHeight(150)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)->build(); ?>";
});
```

```blade
{{-- Usage in Blade templates --}}
<img src="@imgproxy($product->image)" alt="Product">
<img src="@avatar($user->avatar)" alt="User Avatar">
```

#### Eloquent Accessors

Add image processing to Eloquent models:

```php
class User extends Model
{
    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return '/default-avatar.png';
        }

        return imgproxy($this->avatar)
            ->setWidth(150)
            ->setHeight(150)
            ->setResizeType(ResizeType::FILL)
            ->setExtension(OutputExtension::WEBP)
            ->setQuality(85)
            ->build();
    }
}
```

#### API Resources

Use in API resources for consistent image URLs:

```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => [
                'small' => imgproxy($this->avatar)->setWidth(50)->setHeight(50)->build(),
                'medium' => imgproxy($this->avatar)->setWidth(150)->setHeight(150)->build(),
                'large' => imgproxy($this->avatar)->setWidth(300)->setHeight(300)->build(),
            ],
        ];
    }
}
```

### Common Patterns

#### Avatar Processing

```php
class UserAvatar
{
    public static function generate(string $imageUrl, int $size = 150): string
    {
        return imgproxy($imageUrl)
            ->setWidth($size)
            ->setHeight($size)
            ->setResizeType(ResizeType::FILL)
            ->setExtension(OutputExtension::WEBP)
            ->setQuality(85)
            ->setSharpen(0.5)
            ->build();
    }
}

// Usage
$avatarUrl = UserAvatar::generate($user->profile_image, 200);
```

#### Responsive Images

Generate multiple image sizes for responsive images:

```php
class ResponsiveImage
{
    public static function generateSrcset(string $imageUrl, array $sizes): array
    {
        $srcset = [];

        foreach ($sizes as $width) {
            $url = imgproxy($imageUrl)
                ->setWidth($width)
                ->setHeight(intval($width * 0.75)) // 4:3 aspect ratio
                ->setResizeType(ResizeType::FILL)
                ->setExtension(OutputExtension::WEBP)
                ->setQuality(85)
                ->build();

            $srcset[] = "{$url} {$width}w";
        }

        return $srcset;
    }
}

// Usage
$sizes = [400, 800, 1200, 1600];
$srcset = ResponsiveImage::generateSrcset($image, $sizes);
$srcsetString = implode(', ', $srcset);
```

```blade
<img src="{{ imgproxy($image)->setWidth(800)->build() }}"
     srcset="{{ $srcsetString }}"
     sizes="(max-width: 768px) 100vw, 50vw"
     alt="Responsive image">
```

### Image Processing Recipes

#### Photo Enhancement

```php
// Portrait enhancement
$enhancedPortrait = imgproxy($portrait)
    ->setWidth(600)
    ->setHeight(800)
    ->setResizeType(ResizeType::FILL)
    ->setBrightness(8)       // Slightly brighter
    ->setContrast(1.1)       // Enhanced contrast
    ->setSaturation(1.05)    // Subtle saturation boost
    ->setSharpen(0.8)        // Gentle sharpening
    ->setQuality(92)
    ->build();

// High contrast black and white
$highContrastBW = imgproxy($image)
    ->setWidth(800)
    ->setHeight(600)
    ->setSaturation(0)       // Remove all color
    ->setContrast(1.5)       // High contrast
    ->setBrightness(-5)      // Slightly darker
    ->setSharpen(2.0)        // Sharp details
    ->build();
```

#### E-commerce Optimization

```php
// Clean product photos
$productClean = imgproxy($product)
    ->setWidth(800)
    ->setHeight(800)
    ->setResizeType(ResizeType::FIT)
    ->setBrightness(15)      // Bright and clean
    ->setContrast(1.1)       // Good contrast
    ->setSharpen(1.5)        // Sharp product details
    ->setQuality(95)         // High quality for products
    ->setExtension(OutputExtension::WEBP)
    ->build();
```

### Security Best Practices

#### Environment Configuration

```bash
# Use strong, unique keys
IMGPROXY_KEY=$(openssl rand -hex 32)
IMGPROXY_SALT=$(openssl rand -hex 32)

# Use HTTPS in production
IMGPROXY_ENDPOINT=https://imgproxy.yoursite.com

# Consider using encoded mode for security
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
```

#### URL Validation

Always validate source URLs before processing:

```php
class ImageProcessor
{
    private array $allowedDomains = [
        'your-cdn.com',
        'storage.googleapis.com',
        's3.amazonaws.com',
    ];

    public function processImage(string $imageUrl): string
    {
        $parsedUrl = parse_url($imageUrl);

        if (!in_array($parsedUrl['host'], $this->allowedDomains)) {
            throw new InvalidArgumentException('Image domain not allowed');
        }

        return imgproxy($imageUrl)
            ->setWidth(800)
            ->setHeight(600)
            ->setQuality(85)
            ->build();
    }
}
```

## Testing

### Unit & Integration Tests

```bash
# Run all tests
composer test

# Run only unit tests
composer test --filter=Unit

# Run only integration tests
composer test --filter=WorkbenchIntegrationTest

# Run specific test file
composer test --filter=UrlGenerationTest

# Run with coverage
composer test-coverage
```

### Interactive Testing with Workbench

The package includes a comprehensive workbench environment for interactive testing:

```bash
# Build and start the workbench server
composer start

# Or build separately and serve
composer build
php vendor/bin/testbench serve
```

Once the server is running (typically at `http://localhost:8000`), you can access:

#### API Test Endpoints

- **Test Overview**: `http://localhost:8000/imgproxy-test/` - JSON overview of all available tests
- **Basic Test**: `http://localhost:8000/imgproxy-test/basic` - Basic URL generation testing
- **Effects Test**: `http://localhost:8000/imgproxy-test/effects` - Quality and visual effects testing
- **Formats Test**: `http://localhost:8000/imgproxy-test/formats` - Format conversion (JPEG, PNG, WebP, AVIF)
- **Resize Test**: `http://localhost:8000/imgproxy-test/resize` - Different resize types comparison
- **Facade vs Helper**: `http://localhost:8000/imgproxy-test/facade-vs-helper` - Compare facade and helper output
- **Config Test**: `http://localhost:8000/imgproxy-test/config` - Configuration validation
- **Error Handling**: `http://localhost:8000/imgproxy-test/error-handling` - Error scenarios testing
- **Performance Test**: `http://localhost:8000/imgproxy-test/performance` - Performance benchmarks

#### Visual Testing

- **Visual Test Suite**: `http://localhost:8000/imgproxy-visual-test` - Complete browser-based visual testing

The visual test page includes:
- **Real Image Processing** - See actual ImgProxy results with sample images
- **Quality Comparison** - Side-by-side quality levels (30%, 70%, 95%)
- **Format Comparison** - Visual differences between JPEG, PNG, WebP, AVIF
- **Resize Types Demo** - Visual behavior of fit, fill, force, auto modes
- **Effects Showcase** - Blur, sharpen, saturation, brightness effects
- **Complex Processing** - Portrait enhancement and vintage effects
- **High DPI Examples** - Standard vs 2x DPI comparisons

### Test Coverage

The package includes **71 comprehensive tests** with **195 assertions** covering:

- ✅ **Unit Tests** (50 tests) - Organized by functionality:
  - `UrlGenerationTest` - Signed URLs, helper, fluent methods
  - `ValidationTest` - DPR, quality, blur, sharpen, brightness, contrast, saturation
  - `S3UrlTest` - S3 URL handling
  - `ExceptionTest` - Invalid hex key/salt exceptions
  - `EffectsTest` - Quality and effects in URL building
  - `GravityEnumTest` - Gravity enum cases and defaults
  - `GravityMethodsTest` - gravity() and crop() methods
- ✅ **Integration Tests** (13 tests) - Laravel environment, HTTP endpoints, service provider registration
- ✅ **Architecture Tests** (7 tests) - Code structure, security, conventions
- ✅ **Visual Tests** - Browser-based real image processing validation
- ✅ **Performance Tests** - URL generation speed benchmarks (>1000 URLs/second)

### Sample Test Responses

**Basic Test Response:**
```json
{
    "original": "https://picsum.photos/800/600",
    "processed": "http://localhost:8080/signed-url/width:400/height:300/...",
    "test": "basic_url_generation"
}
```

**Performance Test Response:**
```json
{
    "urls_generated": 100,
    "duration_seconds": 0.0089,
    "urls_per_second": 1123.6,
    "test": "performance"
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [SECURITY](SECURITY.md) for details on how to report security vulnerabilities.

## Credits

-   [Imam Susanto](https://github.com/imsus)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
