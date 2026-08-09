---
title: Effects
description: Apply blur, sharpen, pixelate, rotation, watermark, and background color.
---

# Effects

Sometimes you want to do more than resize and compress. The builder provides methods for blur, sharpen, pixelate, rotation, background color, and watermarks — useful for everything from subtle product photography polish to privacy blurring and branded watermarks.

## Blur

Apply a Gaussian blur. The sigma parameter controls the blur radius — higher values produce a stronger effect:

```php
Imgproxy::url($source)->blur(1.5)->url();
// bl:1.5
```

Values between 0.1 and 10 are typical. Use low values for subtle softening and high values for obfuscation or background effects:

```php
// Subtle softening
Imgproxy::url($source)->blur(0.5)->url();

// Strong blur for privacy
Imgproxy::url($source)->blur(8)->url();
```

## Sharpen

Apply a sharpening filter. The sigma parameter controls the mask size — as an approximate guideline, use 0.5 for 4 px/mm, 1.0 for 12 px/mm, and 1.5 for 16 px/mm:

```php
Imgproxy::url($source)->sharpen(0.5)->url();
// sh:0.5
```

Recommended values are between 0.1 and 3:

```php
// Subtle sharpening for product photos
Imgproxy::url($source)->sharpen(0.5)->url();

// Strong sharpening for detailed images
Imgproxy::url($source)->sharpen(2.0)->url();
```

## Pixelate

Apply a pixelation effect. The size parameter sets the side length of each pixel block:

```php
Imgproxy::url($source)->pixelate(4)->url();
// pix:4
```

## Rotate

Rotate the image by a multiple of 90 degrees. Non-multiples or negative values throw `InvalidArgumentException`:

```php
Imgproxy::url($source)->rotate(90)->url();
// rot:90

Imgproxy::url($source)->rotate(180)->url();
// rot:180

Imgproxy::url($source)->rotate(270)->url();
// rot:270
```

## Auto Rotate

Automatically rotate the image based on the EXIF orientation tag. When enabled, images from cameras and phones are displayed in the correct orientation:

```php
Imgproxy::url($source)->autoRotate(true)->url();
// ar:1

Imgproxy::url($source)->autoRotate(false)->url();
// ar:0
```

## Background

Fill the background with a color when the image is extended, padded, or has transparency. Accepts a 3 or 6 digit hex value with an optional leading `#`:

```php
Imgproxy::url($source)->background('#f8fafc')->url();
// bg:f8fafc

Imgproxy::url($source)->background('ffffff')->url();
// bg:ffffff
```

## Watermark

Overlay a watermark image. The first argument is opacity (0–1). Position, offset, and scale are optional:

```php
use Imsus\LaravelImgproxy\Enums\WatermarkPosition;

// 50% opacity, south-east corner, 10px offsets
Imgproxy::url($source)
    ->watermark(0.5, WatermarkPosition::SouthEast, 10, 10)
    ->url();
// wm:0.5:soea:10:10
```

When you need only position and offsets without an explicit scale:

```php
// Center (default), 5px horizontal offset, 10px vertical offset
Imgproxy::url($source)
    ->watermark(0.8, WatermarkPosition::Center, 5, 10)
    ->url();
// wm:0.8:ce:5:10
```

With a custom scale:

```php
// 70% opacity, repeat tiling, scale factor 2
Imgproxy::url($source)
    ->watermark(0.7, WatermarkPosition::Repeat, 0, 0, 2)
    ->url();
// wm:0.7:re:0:0:2
```

::: tip
When you pass offsets or scale without an explicit position, the position defaults to `Center`.
:::

### Available Watermark Positions

| Value | Segment | Description |
|-------|---------|-------------|
| `ce` | `wm:…:ce` | Center (default) |
| `no` | `wm:…:no` | North (top edge) |
| `so` | `wm:…:so` | South (bottom edge) |
| `ea` | `wm:…:ea` | East (right edge) |
| `we` | `wm:…:we` | West (left edge) |
| `nowe` | `wm:…:nowe` | North-west (top-left) |
| `noea` | `wm:…:noea` | North-east (top-right) |
| `sowe` | `wm:…:sowe` | South-west (bottom-left) |
| `soea` | `wm:…:soea` | South-east (bottom-right) |
| `re` | `wm:…:re` | Repeat and tile the watermark |

## Combining Effects

Chain effects in any order — they are appended as segments in call order:

```php
$url = Imgproxy::url($source)
    ->blur(1.5)
    ->sharpen(0.5)
    ->rotate(90)
    ->background('#f8fafc')
    ->url();
// bl:1.5/sh:0.5/rot:90/bg:f8fafc
```

See also: [Usage](/guide/usage), [API Reference](/reference/api)
