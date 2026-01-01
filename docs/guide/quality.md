# Quality & Format

## Output Quality

Set the compression quality (0-100):

```php
$url = imgproxy($url)
    ->setQuality(85)
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
    ->setWidth(150)
    ->setHeight(150)
    ->setQuality(70)
    ->build();

// Hero image - balance quality and size
$hero = imgproxy($image)
    ->setWidth(1200)
    ->setHeight(600)
    ->setQuality(85)
    ->build();

// Product image - prioritize quality
$product = imgproxy($image)
    ->setWidth(800)
    ->setHeight(600)
    ->setQuality(95)
    ->build();
```

## Output Format

Specify the output image format:

```php
use Imsus\ImgProxy\Enums\OutputExtension;

$url = imgproxy($url)
    ->setExtension(OutputExtension::WEBP)
    ->build();
```

### Available Formats

| Format | Enum Value | Best For |
|--------|------------|----------|
| JPEG | `JPEG` | Photographs, general use |
| PNG | `PNG` | Transparency, graphics |
| WebP | `WEBP` | Modern browsers, best compression |
| AVIF | `AVIF` | Best compression, newest format |
| GIF | `GIF` | Animations |
| ICO | `ICO` | Favicons |
| SVG | `SVG` | Vector images |
| HEIC | `HEIC` | Apple devices |
| BMP | `BMP` | Windows compatibility |
| TIFF | `TIFF` | High-quality printing |

### Format Selection Strategy

```php
// Modern browsers - use AVIF for best compression
$avifUrl = imgproxy($image)
    ->setExtension(OutputExtension::AVIF)
    ->setQuality(75)
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

## Quality by Format

Different formats handle quality differently:

```php
// JPEG - quality directly affects compression
imgproxy($url)->setQuality(85)->setExtension(OutputExtension::JPEG)->build();

// WebP - good quality at smaller sizes
imgproxy($url)->setQuality(80)->setExtension(OutputExtension::WEBP)->build();

// AVIF - lower quality still looks great
imgproxy($url)->setQuality(70)->setExtension(OutputExtension::AVIF)->build();
```

## Complete Example

```php
use Imsus\ImgProxy\Enums\OutputExtension;

$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->build();
```
