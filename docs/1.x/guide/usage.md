# Basic Usage

## Helper Function

The easiest way to generate an image URL is using the `imgproxy()` helper function:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(300)
    ->height(200)
    ->build();
```

## Facade

You can also use the facade for static access:

```php
use Imsus\ImgProxy\Facades\ImgProxy;

$url = ImgProxy::url('https://example.com/image.jpg')
    ->width(300)
    ->height(200)
    ->build();
```

## Complete Example

Here's a complete example with all available options:

```php
use Imsus\ImgProxy\Facades\ImgProxy;

$url = ImgProxy::url('https://example.com/image.jpg')
    ->width(800)
    ->height(600)
    ->cover()
    ->webp()
    ->quality(85)
    ->dpr(2)
    ->blur(2.0)
    ->sharpen(1.5)
    ->build();
```

## Result

The `build()` method returns the complete, signed imgproxy URL:

```php
"http://localhost:8080/signed-url/width:800/height:600/quality:85/..."
```

## Fluent API

The fluent API provides convenient method aliases for common operations:

### Method Aliases

Shorter alternatives to setter methods:

```php
$url = imgproxy('image.jpg')
    ->width(300)      // setWidth(300)
    ->height(200)     // setHeight(200)
    ->quality(85)     // setQuality(85)
    ->dpr(2)          // setDpr(2)
    ->build();
```

### Format Shortcuts

Quickly change output format without enums:

```php
$url = imgproxy('image.jpg')
    ->width(800)
    ->height(600)
    ->webp()          // Output as WebP
    ->build();

// Or other formats
->avif()             // Output as AVIF
->png()              // Output as PNG
->jpg()              // Output as JPEG
```

### Fit Shortcuts

Common resize modes as easy-to-use methods:

```php
$url = imgproxy('image.jpg')
    ->width(800)
    ->height(600)
    ->cover()         // Fill area (ResizeType::FILL)
    ->build();

// Other options
->contain()          // Fit within area (ResizeType::FIT)
->fill()             // Force dimensions (ResizeType::FILL_DOWN)
```

### Cache Busting

Use the `v()` method to invalidate cached images:

```php
$url = imgproxy('image.jpg')
    ->width(300)
    ->height(200)
    ->v(2)            // Version 2 - forces CDN refresh
    ->build();
```

## Method Chaining

The fluent API allows you to chain methods in any order:

```php
$url = imgproxy($image)
    ->blur(2.0)
    ->width(800)
    ->quality(85)
    ->sharpen(1.0)
    ->height(600)
    ->build();
```

## Error Handling

The package includes comprehensive validation and will throw `InvalidArgumentException` for invalid parameters:

```php
try {
    $url = imgproxy('invalid-url')
        ->quality(150)  // Invalid: > 100
        ->build();
} catch (InvalidArgumentException $e) {
    // Handle validation error
    echo $e->getMessage(); // "Quality must be between 0 and 100"
}
```

For invalid URLs, the package gracefully returns the original URL instead of throwing an exception.

## Backward Compatibility

All `set*` methods remain fully supported:

```php
// Old style still works
$url = imgproxy('image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)
    ->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::WEBP)
    ->build();
```

The fluent API aliases (`width()`, `height()`, etc.) call the same underlying methods, so you can mix both styles in your codebase.
