# Configuration

## Available Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `endpoint` | `string` | `http://localhost:8080` | imgproxy server URL |
| `key` | `string\|null` | `null` | Hex-encoded HMAC signing key |
| `salt` | `string\|null` | `null` | Hex-encoded HMAC signing salt |
| `default_source_url_mode` | `string` | `encoded` | Default URL encoding mode |
| `default_output_extension` | `string` | `jpeg` | Default output format |

## Environment Variables

```env
IMGPROXY_ENDPOINT=http://localhost:8080
IMGPROXY_KEY=your_hex_key_here
IMGPROXY_SALT=your_hex_salt_here
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=jpeg
```

## Accessing Configuration

```php
// Get the full config
$config = config('laravel-imgproxy');

// Get specific values
$endpoint = config('laravel-imgproxy.endpoint');
$key = config('laravel-imgproxy.key');
```

## Programmatic Configuration

You can modify configuration at runtime:

```php
config(['laravel-imgproxy.endpoint' => 'https://imgproxy.yoursite.com']);
config(['laravel-imgproxy.key' => 'new_key']);
```

## Helper Function

Get an instance of imgproxy:

```php
imgproxy(); // Returns imgproxy builder instance
```

## Facade

```php
use Imsus\ImgProxy\Facades\ImgProxy;

ImgProxy::url($url); // Create new URL builder
```

# API Reference

## Builder Methods

| Method | Parameters | Description |
|--------|------------|-------------|
| `url(string $url)` | Image URL | Set the source image URL |
| `setWidth(int $width)` | Width in pixels | Set image width (1-5000) |
| `setHeight(int $height)` | Height in pixels | Set image height (1-5000) |
| `setResizeType(ResizeType $type)` | Resize mode | Set how image should be resized |
| `setExtension(OutputExtension $ext)` | Output format | Set output image format |
| `setDpr(int $dpr)` | 1-8 | Set device pixel ratio |
| `setQuality(int $quality)` | 0-100 | Set compression quality |
| `setBlur(float $sigma)` | >= 0.0 | Apply blur effect |
| `setSharpen(float $sigma)` | >= 0.0 | Apply sharpen effect |
| `setMode(SourceUrlMode $mode)` | `encoded`/`plain` | Set URL encoding mode |
| `setGravity(Gravity $gravity)` | Gravity position | Set gravity for crop/fill |
| `setProcessing(string $options)` | Processing string | Custom processing options |
| `build()` | - | Generate final URL |

## Usage Examples

### Basic URL Generation

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();
```

### With All Options

```php
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\Gravity;

$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->setDpr(2)
    ->setGravity(Gravity::CENTER)
    ->setBlur(2.0)
    ->setSharpen(1.0)
    ->build();
```

### With Custom Processing

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setProcessing('rs:fill:400:300:1/rt:fit/q:85/bl:2.0')
    ->build();
```
