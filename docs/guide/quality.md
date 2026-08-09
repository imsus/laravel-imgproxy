---
title: Quality & Format
description: Control output quality, format conversion, DPR, and metadata stripping.
---

# Quality & Format

The builder provides methods for compression quality, output format conversion, per-format quality overrides, device pixel ratio, metadata stripping, and response behavior.

## Quality

Set the compression quality (0–100). Lower values produce smaller files at the cost of visual fidelity:

```php
Imgproxy::url($source)->quality(80)->url();
// q:80
```

## Format

Convert the output to a specific image format. Pass a `Format` enum or its string value:

```php
use Imsus\LaravelImgproxy\Enums\Format;

Imgproxy::url($source)->format(Format::Webp)->url();
// f:webp
```

### Available Formats

| Value | Segment | Best for |
|-------|---------|----------|
| `jpg` | `f:jpg` | Photographs, general use |
| `png` | `f:png` | Transparency, graphics |
| `webp` | `f:webp` | Modern browsers, best compression |
| `avif` | `f:avif` | Best compression, newest format |
| `gif` | `f:gif` | Animations |
| `ico` | `f:ico` | Favicons |
| `svg` | `f:svg` | Vector images |
| `bmp` | `f:bmp` | Windows compatibility |
| `tiff` | `f:tiff` | High-quality printing |
| `heic` | `f:heic` | Apple devices |
| `jxl` | `f:jxl` | JPEG XL, next-gen compression |

### Format Selection Strategy

Use the `<x-imgproxy-picture>` Blade component for automatic format negotiation, or pick manually:

```php
// Modern browsers
Imgproxy::url($source)->format(Format::Avif)->quality(75)->url();

// Wide compatibility
Imgproxy::url($source)->format(Format::Webp)->quality(80)->url();

// Universal fallback
Imgproxy::url($source)->format(Format::Jpg)->quality(90)->url();
```

## Format Quality

Override the quality for specific formats. The argument is an associative array mapping format strings to quality values:

```php
Imgproxy::url($source)
    ->formatQuality(['webp' => 80, 'avif' => 65])
    ->url();
// fq:webp:80/avif:65
```

This lets you set different compression targets per format — AVIF tolerates lower quality than JPEG, for example.

## Skip Processing

Skip processing for specific formats. The listed formats pass through unchanged:

```php
use Imsus\LaravelImgproxy\Enums\Format;

Imgproxy::url($source)
    ->skipProcessing(Format::Svg, Format::Gif)
    ->url();
// sk:svg:gif
```

## Raw Response

When enabled, imgproxy returns the processed image without transformation headers:

```php
Imgproxy::url($source)->rawResponse()->url();
// raw:1

Imgproxy::url($source)->rawResponse(false)->url();
// raw:0
```

## Device Pixel Ratio

Multiply the image dimensions by a factor for HiDPI (Retina) displays. The browser's CSS pixel size stays the same; the raster image is scaled up:

```php
// 2× for Retina displays
Imgproxy::url($source)->width(400)->dpr(2)->url();
// w:400/dpr:2
// Actual output: 800px wide
```

## Strip Metadata

Strip EXIF, IPTC, and other metadata from the output:

```php
Imgproxy::url($source)->stripMetadata(true)->url();
// sm:1
```

## Keep Copyright

Preserve copyright info when stripping metadata. Only useful in combination with `stripMetadata()`:

```php
Imgproxy::url($source)
    ->stripMetadata(true)
    ->keepCopyright(true)
    ->url();
// sm:1/kcr:1
```

## Strip Color Profile

Transform the embedded color profile to sRGB and remove it from the image:

```php
Imgproxy::url($source)->stripColorProfile(true)->url();
// scp:1
```

## Preserve HDR

Keep high-bit images as high-bit instead of downscaling them to 8-bit:

```php
Imgproxy::url($source)->preserveHdr(true)->url();
// ph:1
```

### Complete Example

```php
use Imsus\LaravelImgproxy\Enums\Format;

$url = Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->format(Format::Webp)
    ->quality(80)
    ->formatQuality(['webp' => 75, 'avif' => 60])
    ->dpr(2)
    ->stripMetadata(true)
    ->stripColorProfile(true)
    ->url();
```

See also: [Usage](/guide/usage), [API Reference](/reference/api)
