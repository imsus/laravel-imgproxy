# Enums Reference

## ResizeType

Controls how images are resized to fit the specified dimensions.

```php
use Imsus\ImgProxy\Enums\ResizeType;
```

| Enum Value | Fluent Method | Description |
|------------|---------------|-------------|
| `FIT` | `contain()` | Resize keeping aspect ratio to fit within dimensions (default) |
| `FILL` | `cover()` | Resize keeping aspect ratio to fill dimensions, cropping overflow |
| `FILL_DOWN` | `fill()` | Same as fill, but maintains aspect ratio for smaller images |
| `FORCE` | - | Resize without keeping aspect ratio (stretches/distorts) |
| `AUTO` | - | Automatically choose between fit/fill based on orientation |

### Example

```php
// Using fluent method
imgproxy($url)
    ->cover()
    ->width(300)
    ->height(200)
    ->build();

// Using enum directly
imgproxy($url)
    ->setResizeType(ResizeType::FILL)
    ->width(300)
    ->height(200)
    ->build();
```

## OutputExtension

Specifies the output image format.

```php
use Imsus\ImgProxy\Enums\OutputExtension;
```

| Enum Value | Fluent Method | Format |
|------------|---------------|--------|
| `JPEG` | `jpg()` | JPEG format |
| `PNG` | `png()` | PNG format |
| `WEBP` | `webp()` | WebP format |
| `AVIF` | `avif()` | AVIF format |
| `GIF` | - | GIF format |
| `ICO` | - | ICO format |
| `SVG` | - | SVG format |
| `HEIC` | - | HEIC format |
| `BMP` | - | BMP format |
| `TIFF` | - | TIFF format |

### Example

```php
// Using fluent method
imgproxy($url)
    ->webp()
    ->quality(85)
    ->build();

// Using enum directly
imgproxy($url)
    ->setExtension(OutputExtension::WEBP)
    ->quality(85)
    ->build();
```

## SourceUrlMode

Controls how the source URL is encoded in the generated URL.

```php
use Imsus\ImgProxy\Enums\SourceUrlMode;
```

| Enum Value | Description |
|------------|-------------|
| `ENCODED` | Base64 encode source URL (default, recommended) |
| `PLAIN` | Use plain text URL (for debugging only) |

### Example

```php
imgproxy($url)
    ->setMode(SourceUrlMode::PLAIN)
    ->width(300)
    ->build();
```

## Gravity

Controls which part of the image is kept when using FILL resize type.

```php
use Imsus\ImgProxy\Enums\Gravity;
```

| Enum Value | Description |
|------------|-------------|
| `CE` | Center (default) |
| `NO` | North (top) |
| `SO` | South (bottom) |
| `EA` | East (right) |
| `WE` | West (left) |
| `NOEA` | North-East (top-right) |
| `NOWE` | North-West (top-left) |
| `SOEA` | South-East (bottom-right) |
| `SOWE` | South-West (bottom-left) |
| `AUTO` | Smart cropping based on image content |

### Example

```php
imgproxy($url)
    ->cover()
    ->width(300)
    ->height(200)
    ->gravity(Gravity::CENTER)
    ->build();
```
