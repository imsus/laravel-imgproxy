---
title: Quality & Format
description: Control output quality, format conversion, DPR, and metadata stripping.
---

# Quality & Format

Beyond resizing, the two decisions that matter most for image delivery are compression quality and output format. The builder provides methods for both, plus per-format quality overrides, device pixel ratio, metadata stripping, and response behavior.

## Quality

Set the compression quality (0–100). Lower values produce smaller files at the cost of visual fidelity:

```php
Imgproxy::image($source)->quality(80)->url();
// q:80
```

A quality of `0` makes imgproxy fall back to its own configured quality.

## Format

Convert the output to a specific image format. Pass a `Format` enum or its string value:

```php
use Imsus\LaravelImgproxy\Enums\Format;

Imgproxy::image($source)->format(Format::Webp)->url();
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

### Choosing a Format

If you are serving images to browsers, the `<x-imgproxy-picture>` Blade component will handle format negotiation for you automatically. When you need to pick a format yourself, here is a sensible starting point:

```php
// Modern browsers
Imgproxy::image($source)->format(Format::Avif)->quality(75)->url();

// Wide compatibility
Imgproxy::image($source)->format(Format::Webp)->quality(80)->url();

// Universal fallback
Imgproxy::image($source)->format(Format::Jpg)->quality(90)->url();
```

## Format Quality

Override the quality for specific formats. The argument is an associative array mapping format strings to quality values:

```php
Imgproxy::image($source)
    ->formatQuality(['webp' => 80, 'avif' => 65])
    ->url();
// fq:webp:80:avif:65
```

This lets you set different compression targets per format — AVIF tolerates lower quality than JPEG, for example.

## Skip Processing

Skip processing for specific source formats. The listed formats pass through unchanged:

```php
use Imsus\LaravelImgproxy\Enums\Format;

Imgproxy::image($source)
    ->skipProcessing(Format::Svg, Format::Gif)
    ->url();
// skp:svg:gif
```

## Raw Image

When enabled, imgproxy returns the processed image without transformation headers:

```php
Imgproxy::image($source)->raw()->url();
// raw:1

Imgproxy::image($source)->withoutRaw()->url();
// raw:0
```

## Device Pixel Ratio

Multiply the image dimensions by a factor for HiDPI (Retina) displays. The browser's CSS pixel size stays the same; the raster image is scaled up:

```php
// 2× for Retina displays
Imgproxy::image($source)->width(400)->dpr(2)->url();
// w:400/dpr:2
// Actual output: 800px wide
```

Unlike `zoom()`, `dpr()` also scales gravity offsets and paddings.

## Strip Metadata

Strip EXIF, IPTC, and other metadata from the output:

```php
Imgproxy::image($source)->stripMetadata()->url();
// sm:1
```

## Keep Copyright

Preserve copyright info when stripping metadata. Only useful in combination with `stripMetadata()`:

```php
Imgproxy::image($source)
    ->stripMetadata()
    ->keepCopyright()
    ->url();
// sm:1/kcr:1
```

## Strip Color Profile

Transform the embedded color profile to sRGB and remove it from the image:

```php
Imgproxy::image($source)->stripColorProfile()->url();
// scp:1
```

## Preserve HDR

Keep high-bit images as high-bit instead of downscaling them to 8-bit:

```php
Imgproxy::image($source)->preserveHDR()->url();
// ph:1
```

### A Complete Example

```php
use Imsus\LaravelImgproxy\Enums\Format;

$url = Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->format(Format::Webp)
    ->quality(80)
    ->formatQuality(['webp' => 75, 'avif' => 60])
    ->dpr(2)
    ->stripMetadata()
    ->stripColorProfile()
    ->url();
```

See also: [Usage](/guide/usage), [API Reference](/reference/api)
