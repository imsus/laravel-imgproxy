---
title: Resizing
description: Resize, crop, trim, and pad images with the fluent builder.
---

# Resizing

Resizing is the most common thing you will do with an image service, so the builder has a full set of methods for controlling dimensions, resize type, gravity, cropping, trimming, and padding. Every method returns a new builder instance, so options compose safely.

## Resize Type

The `resize()` method sets the resize type, dimensions, and optional enlarge/extend flags in one call. Pass a `ResizeType` enum or its string value:

```php
use Imsus\LaravelImgproxy\Enums\ResizeType;

$url = Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
// rs:fill:800:600
```

Pass `enlarge: true` to allow the image to be upscaled:

```php
$url = Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600, enlarge: true)
    ->url();
// rs:fill:800:600:1
```

Pass `extend: true` to fill out-of-bounds areas with the background color:

```php
$url = Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600, extend: true)
    ->url();
// rs:fill:800:600:0:1
```

### Available Resize Types

| Value | Segment | Behavior |
|-------|---------|----------|
| `fit` | `rs:fit` | Fit within bounds, keep aspect ratio, no crop |
| `fill` | `rs:fill` | Fill bounds, crop overflow, keep aspect ratio |
| `fill-down` | `rs:fill-down` | Like `fill`, but crop to requested ratio when the image is smaller |
| `force` | `rs:force` | Stretch to exact dimensions, ignoring the aspect ratio |
| `auto` | `rs:auto` | Use `fill` when orientation matches, otherwise `fit` |

```php
// Fit: the image stays within 400x300
Imgproxy::image($source)->resize(ResizeType::Fit, 400, 300)->url();

// Force: stretch to 400x300 regardless of aspect ratio
Imgproxy::image($source)->resize(ResizeType::Force, 400, 300)->url();

// Auto: smart choice based on source/target orientation
Imgproxy::image($source)->resize(ResizeType::Auto, 400, 300)->url();
```

## Resize With Gravity

`resizeWithGravity()` sets width, height, and optional enlarge/extend flags plus a gravity hint, without specifying the resize type. Use it when you want to rely on the server's default resize type:

```php
$url = Imgproxy::image($source)
    ->resizeWithGravity(800, 600, enlarge: true)
    ->url();
// s:800:600:1
```

## Setting the Resize Type

The `resize()` method sets the type together with a size in one call. If you need to set the resize type without dimensions, use `withOption()` with the standalone `rt:` segment:

```php
$url = Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
// rs:fill:800:600

// Or set the type standalone via withOption()
$url = Imgproxy::image($source)
    ->withOption('rt:fill')
    ->width(800)
    ->height(600)
    ->url();
// rt:fill/w:800/h:600
```

## Width & Height

Set width or height independently. The other dimension is auto-calculated to preserve the aspect ratio:

```php
// Width only — height auto-calculated
Imgproxy::image($source)->width(640)->url();
// w:640

// Height only — width auto-calculated
Imgproxy::image($source)->height(480)->url();
// h:480
```

## Min Width & Min Height

Set minimum dimensions. The image will be resized to at least these values:

```php
Imgproxy::image($source)->minWidth(200)->url();
// mw:200

Imgproxy::image($source)->minHeight(150)->url();
// mh:150
```

## Zoom

Magnify a region of the image by a factor. With one argument, both axes use the same factor:

```php
Imgproxy::image($source)->zoom(2)->url();
// z:2

// Different factors for x and y
Imgproxy::image($source)->zoom(2, 1.5)->url();
// z:2:1.5
```

Unlike `dpr()`, `zoom()` does not affect gravity offsets, watermark offsets, or paddings.

## Crop

Crop the image to exact dimensions with an optional gravity hint:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

// Crop to 0.5×0.5 (relative) with north gravity
Imgproxy::image($source)
    ->crop(0.5, 0.5, Gravity::North)
    ->url();
// c:0.5:0.5:no
```

## Trim

Remove the surrounding background. The threshold controls sensitivity; optionally specify a color and equal-cut flags:

```php
// Remove background with 10% threshold
Imgproxy::image($source)->trim(0.1)->url();
// t:0.1

// Trim to a specific color, equal horizontal cut
Imgproxy::image($source)->trim(0.1, '#ffffff', equalHorizontal: true)->url();
// t:0.1:ffffff:1
```

## Padding

Add padding around the processed image using CSS-style shorthand:

```php
// All sides 20px
Imgproxy::image($source)->padding(20)->url();
// pd:20

// top:10, right:20, bottom:10, left:20
Imgproxy::image($source)->padding(10, 20, 10, 20)->url();
// pd:10:20:10:20
```

Omitted sides default as they do in CSS: `right` follows `top`, `bottom` follows `top`, and `left` follows `right`.

## Gravity

Gravity controls which part of the image is kept when imgproxy cuts parts away. It accepts a `Gravity` enum or string value:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

// Smart gravity: libvips detects the most interesting section
Imgproxy::image($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->gravity(Gravity::Smart)
    ->url();
// g:sm

// Compass gravity with offsets
Imgproxy::image($source)
    ->gravity(Gravity::North, 10, 5)
    ->url();
// g:no:10:5
```

Offsets are emitted only when they are non-zero.

### Available Gravity Values

| Value | Segment | Description |
|-------|---------|-------------|
| `ce` | `g:ce` | Center (default) |
| `no` | `g:no` | North (top edge) |
| `so` | `g:so` | South (bottom edge) |
| `ea` | `g:ea` | East (right edge) |
| `we` | `g:we` | West (left edge) |
| `nowe` | `g:nowe` | North-west (top-left) |
| `noea` | `g:noea` | North-east (top-right) |
| `sowe` | `g:sowe` | South-west (bottom-left) |
| `soea` | `g:soea` | South-east (bottom-right) |
| `sm` | `g:sm` | Smart: content-aware detection |

## Focus Point

Set an exact focus point as normalized coordinates (0–1), where `0` is left/top and `1` is right/bottom:

```php
Imgproxy::image($source)
    ->focusPoint(0.3, 0.7)
    ->url();
// g:fp:0.3:0.7
```

## Flip

Flip the image along the horizontal and/or vertical axis:

```php
// Horizontal flip
Imgproxy::image($source)->flip(horizontal: true)->url();
// fl:1:0

// Vertical flip
Imgproxy::image($source)->flip(vertical: true)->url();
// fl:0:1

// Both axes
Imgproxy::image($source)->flip(horizontal: true, vertical: true)->url();
// fl:1:1
```

## Enlarge

Allow or disallow upscaling when the image is smaller than the target size:

```php
Imgproxy::image($source)->enlarge()->url();
// el:1
```

## Extend

Extend the image when it is smaller than the target size, filling with the background color:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::image($source)
    ->extend(Gravity::Center)
    ->url();
// ex:1:ce
```

## Extend Aspect Ratio

Extend the image to match the requested aspect ratio, filling with the background color:

```php
Imgproxy::image($source)
    ->extendAspectRatio(Gravity::North)
    ->url();
// exar:1:no
```

::: tip
Gravity for `extend()` and `extendAspectRatio()` does not accept `Smart` — passing it throws `InvalidArgumentException`.
:::

See also: [Usage](/guide/usage), [API Reference](/reference/api)
