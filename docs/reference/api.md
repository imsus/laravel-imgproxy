# Configuration

## Available Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `endpoint` | `string` | `http://localhost:8080` | imgproxy server URL |
| `key` | `string\|null` | `null` | Hex-encoded HMAC signing key |
| `salt` | `string\|null` | `null` | Hex-encoded HMAC signing salt |
| `default_source_url_mode` | `string` | `encoded` | Default URL encoding mode |
| `default_output_extension` | `string` | `jpeg` | Default output format |
| `use_short_options` | `bool` | `false` | Use short option names (`w` instead of `width`) |
| `fallback_url` | `string\|null` | `null` | Global fallback image URL |

## Environment Variables

```env
IMGPROXY_ENDPOINT=http://localhost:8080
IMGPROXY_KEY=your_hex_key_here
IMGPROXY_SALT=your_hex_salt_here
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=jpeg
IMGPROXY_USE_SHORT_OPTIONS=false
IMGPROXY_FALLBACK_URL=
```

## Short Option Names

When `use_short_options` is enabled, URLs use abbreviated parameter names for cleaner output:

```php
// With use_short_options => false (default)
$url = imgproxy('image.jpg')->width(300)->height(200)->build();
// Output: /width:300/height:200/...

// With use_short_options => true
$url = imgproxy('image.jpg')->width(300)->height(200)->build();
// Output: /w:300/h:200/...
```

## Fallback URL

Set a global fallback image for when the source URL is missing or invalid:

```php
// In config/laravel-imgproxy.php
'fallback_url' => 'https://example.com/placeholder.jpg'

// Or per-request
$url = imgproxy('missing-image.jpg')
    ->fallback('https://example.com/placeholder.jpg')
    ->width(300)
    ->build();
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

### Fluent API (Recommended)

| Method | Parameters | Description |
|--------|------------|-------------|
| `url(string $url)` | Image URL | Set the source image URL |
| `width(int $width)` | Width in pixels | Set image width (1-5000) |
| `height(int $height)` | Height in pixels | Set image height (1-5000) |
| `quality(int $quality)` | 0-100 | Set compression quality |
| `dpr(int $dpr)` | 1-8 | Set device pixel ratio |
| `webp()` | - | Output as WebP |
| `avif()` | - | Output as AVIF |
| `png()` | - | Output as PNG |
| `jpg()` | - | Output as JPEG |
| `cover()` | - | Set resize type to fill |
| `contain()` | - | Set resize type to fit |
| `fill()` | - | Set resize type to fill-down |
| `blur(float $sigma)` | >= 0.0 | Apply blur effect |
| `sharpen(float $sigma)` | >= 0.0 | Apply sharpen effect |
| `gravity(Gravity $gravity)` | Gravity position | Set gravity for crop/fill |
| `v(int\|string $version)` | Version | Cache busting version |
| `fallback(string $url)` | Fallback URL | Set fallback image |
| `build()` | - | Generate final URL |

### Setter Methods (Legacy)

| Method | Parameters | Description |
|--------|------------|-------------|
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

## Usage Examples

### Basic URL Generation

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(300)
    ->height(200)
    ->build();
```

### With All Options

```php
use Imsus\ImgProxy\Enums\Gravity;

$url = imgproxy('https://example.com/image.jpg')
    ->width(800)
    ->height(600)
    ->cover()
    ->webp()
    ->quality(85)
    ->dpr(2)
    ->gravity(Gravity::CENTER)
    ->blur(2.0)
    ->sharpen(1.0)
    ->build();
```

### With Custom Processing

```php
$url = imgproxy('https://example.com/image.jpg')
    ->processing('rs:fill:400:300:1/rt:fit/q:85/bl:2.0')
    ->build();
```

### With Fallback

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(300)
    ->height(200)
    ->fallback('https://example.com/placeholder.jpg')
    ->build();
```

### Cache Busting

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(300)
    ->v(2)  // Increment when you need to force refresh
    ->build();
```
