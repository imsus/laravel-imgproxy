# Quality & Format

## Output Quality

Set the compression quality (0-100):

```php
$url = imgproxy($url)
    ->quality(85)
    ->build();
```

### Quality Guidelines

| Quality | Use Case |
|---------|----------|
| 60-70 | Thumbnails, small images |
| 75-85 | Standard web images |
| 90+ | High-quality images, photography |

```php
// Thumbnail - smaller file size
$thumbnail = imgproxy($image)
    ->width(150)
    ->height(150)
    ->quality(70)
    ->build();

// Hero image - balance quality and size
$hero = imgproxy($image)
    ->width(1200)
    ->height(600)
    ->quality(85)
    ->build();

// Product image - prioritize quality
$product = imgproxy($image)
    ->width(800)
    ->height(600)
    ->quality(95)
    ->build();
```

## Output Format

Specify the output image format using format shortcuts or the enum:

```php
// Using fluent shortcuts
$url = imgproxy($url)
    ->webp()
    ->build();

// Using enum
use Imsus\ImgProxy\Enums\OutputExtension;

$url = imgproxy($url)
    ->setExtension(OutputExtension::WEBP)
    ->build();
```

### Available Formats

| Format | Shortcut | Enum Value | Best For |
|--------|----------|------------|----------|
| JPEG | `jpg()` | `JPEG` | Photographs, general use |
| PNG | `png()` | `PNG` | Transparency, graphics |
| WebP | `webp()` | `WEBP` | Modern browsers, best compression |
| AVIF | `avif()` | `AVIF` | Best compression, newest format |
| GIF | - | `GIF` | Animations |
| ICO | - | `ICO` | Favicons |
| SVG | - | `SVG` | Vector images |
| HEIC | - | `HEIC` | Apple devices |
| BMP | - | `BMP` | Windows compatibility |
| TIFF | - | `TIFF` | High-quality printing |

### Format Selection Strategy

```php
// Modern browsers - use AVIF for best compression
$avifUrl = imgproxy($image)
    ->avif()
    ->quality(75)
    ->build();

// Fallback for older browsers - use WebP
$webpUrl = imgproxy($image)
    ->webp()
    ->quality(85)
    ->build();

// Universal fallback - use JPEG
$jpegUrl = imgproxy($image)
    ->jpg()
    ->quality(90)
    ->build();
```

## Quality by Format

Different formats handle quality differently:

```php
// JPEG - quality directly affects compression
imgproxy($url)->quality(85)->jpg()->build();

// WebP - good quality at smaller sizes
imgproxy($url)->quality(80)->webp()->build();

// AVIF - lower quality still looks great
imgproxy($url)->quality(70)->avif()->build();
```

## Complete Example

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(800)
    ->height(600)
    ->webp()
    ->quality(85)
    ->build();
```
