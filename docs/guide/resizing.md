---
title: Resizing
description: Resize, crop, trim, and pad images with the fluent builder.
---

# Resizing

The builder provides a full set of methods for controlling image dimensions, resize type, gravity, cropping, trimming, and padding. Every method returns a new builder instance, so options compose safely.

## Resize Type

The `resize()` method sets the resize type, dimensions, and optional enlarge/extend flags in one call. Pass a `ResizeType` enum or its string value:

```php
use Imsus\LaravelImgproxy\Enums\ResizeType;

$url = Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
// rs:fill:800:600
```

Pass `enlarge: true` to allow the image to be upscaled:

```php
$url = Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600, enlarge: true)
    ->url();
// rs:fill:800:600:1
```

Pass `extend: true` to fill out-of-bounds areas with the background color:

```php
$url = Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600, extend: true)
    ->url();
// rs:fill:800:600:0:1
```

### Available Resize Types

| Value | Segment | Behavior |
|-------|---------|----------|
| `fit` | `rs:fit` | Fit within bounds, keep aspect ratio, no crop |
| `fill` | `rs:fill` | Fill bounds, crop overflow, keep aspect ratio |
| `fill-down` | `rs:fill-down` | Like `fill`, but crop to requested ratio when image is smaller |
| `force` | `rs:force` | Stretch to exact dimensions (ignores aspect ratio) |
| `auto` | `rs:auto` | Use `fill` when orientation matches, otherwise `fit` |

```php
// Fit: image stays within 400x300
Imgproxy::url($source)->resize(ResizeType::Fit, 400, 300)->url();

// Force: stretch to 400x300 regardless of aspect ratio
Imgproxy::url($source)->resize(ResizeType::Force, 400, 300)->url();

// Auto: smart choice based on source/target orientation
Imgproxy::url($source)->resize(ResizeType::Auto, 400, 300)->url();
```

## Size

`size()` sets width, height, and optional enlarge/extend flags plus a gravity hint, without specifying the resize type. Use it when you want to rely on the server's default resize type:

```php
$url = Imgproxy::url($source)
    ->size(800, 600, enlarge: true)
    ->url();
// s:800:600:1
```

## Resizing Type

`resizingType()` sets the resize type without providing dimensions — useful when you want to control only the type and set width/height separately:

```php
$url = Imgproxy::url($source)
    ->resizingType(ResizeType::Fill)
    ->width(800)
    ->height(600)
    ->url();
// rt:fill/w:800/h:600
```

## Width & Height

Set width or height independently. The other dimension is auto-calculated to preserve the aspect ratio:

```php
// Width only — height auto-calculated
Imgproxy::url($source)->width(640)->url();
// w:640

// Height only — width auto-calculated
Imgproxy::url($source)->height(480)->url();
// h:480
```

## Min Width & Min Height

Set minimum dimensions. The image will be resized to at least these values:

```php
Imgproxy::url($source)->minWidth(200)->url();
// mww:200

Imgproxy::url($source)->minHeight(150)->url();
// mwh:150
```

## Zoom

Magnify a region of the image by a factor. With one argument, both axes use the same factor:

```php
Imgproxy::url($source)->zoom(2)->url();
// z:2

// Different factors for x and y
Imgproxy::url($source)->zoom(2, 1.5)->url();
// z:2:1.5
```

## Crop

Crop the image to exact dimensions with an optional gravity hint:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

// Crop to 0.5×0.5 (relative) with north gravity
Imgproxy::url($source)
    ->crop(0.5, 0.5, Gravity::North)
    ->url();
// c:0.5:0.5:no
```

## Trim

Remove the surrounding background. The threshold controls sensitivity; optionally specify a color and equal-cut flags:

```php
// Remove background with 10% threshold
Imgproxy::url($source)->trim(0.1)->url();
// t:0.1

// Trim to a specific color, equal horizontal cut
Imgproxy::url($source)->trim(0.1, '#ffffff', equalHor: true)->url();
// t:0.1:#ffffff:1
```

## Padding

Add padding around the processed image using CSS-style shorthand:

```php
// All sides 20px
Imgproxy::url($source)->padding(20)->url();
// pd:20

// top:10, right:20, bottom:10, left:20
Imgproxy::url($source)->padding(10, 20, 10, 20)->url();
// pd:10:20:10:20
```

## Gravity

Control which part of the image is kept when imgproxy cuts parts away. Accepts a `Gravity` enum or string value:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

// Smart gravity: libvips detects the most interesting section
Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600)
    ->gravity(Gravity::Smart)
    ->url();
// g:sm

// Compass gravity with offsets
Imgproxy::url($source)
    ->gravity(Gravity::North, 10, 5)
    ->url();
// g:no:10:5
```

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

Set an exact focus point as normalized coordinates (0–1) where `0` is left/top and `1` is right/bottom:

```php
Imgproxy::url($source)
    ->focusPoint(0.3, 0.7)
    ->url();
// g:fp:0.3:0.7
```

## Flip

Flip the image along the horizontal and/or vertical axis:

```php
// Horizontal flip
Imgproxy::url($source)->flip(horizontal: true)->url();
// fl:1:0

// Vertical flip
Imgproxy::url($source)->flip(vertical: true)->url();
// fl:0:1

// Both axes
Imgproxy::url($source)->flip(horizontal: true, vertical: true)->url();
// fl:1:1
```

## Enlarge

Allow or disallow upscaling when the image is smaller than the target size:

```php
Imgproxy::url($source)->enlarge(true)->url();
// el:1
```

## Extend

Extend the image when it is smaller than the target size, filling with the background color:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::url($source)
    ->extend(true, Gravity::Center)
    ->url();
// ex:1:ce
```

## Extend Aspect Ratio

Extend the image to match the requested aspect ratio, filling with the background color:

```php
Imgproxy::url($source)
    ->extendAspectRatio(true, Gravity::North)
    ->url();
// exar:1:no
```

::: tip
Gravity for `extend()` and `extendAspectRatio()` does not accept `Smart` — it throws `InvalidArgumentException`.
:::

See also: [Usage](/guide/usage), [API Reference](/reference/api)
