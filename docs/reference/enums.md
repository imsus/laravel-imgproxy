---
title: Enums Reference
description: Backed enums for ResizeType, Gravity, Format, and WatermarkPosition with every case value and its imgproxy segment mapping.
---

# Enums Reference

All four enums live in `Imsus\LaravelImgproxy\Enums` and accept the enum instance **or** its string backing value interchangeably:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

$builder->gravity(Gravity::Smart);  // enum instance
$builder->gravity('sm');            // string value — identical result
```

Invalid strings throw `InvalidArgumentException` with the list of supported values.

## ResizeType

`Imsus\LaravelImgproxy\Enums\ResizeType` — the imgproxy resizing type (`rs:` option).

| Case | Value | Description |
| --- | --- | --- |
| `Fit` | `'fit'` | Resize while keeping the aspect ratio to fit the given size. |
| `Fill` | `'fill'` | Resize while keeping the aspect ratio to fill the given size and crop projecting parts. |
| `FillDown` | `'fill-down'` | Like `fill`, but crop the result to the requested aspect ratio when the resized image is smaller. |
| `Force` | `'force'` | Resize without keeping the aspect ratio. |
| `Auto` | `'auto'` | Use `fill` when the source and result dimensions have the same orientation, otherwise `fit`. |

Used by: [`resize()`](/reference/api#resize), and the `resize` preset key.

```php
use Imsus\LaravelImgproxy\Enums\ResizeType;

$imgproxy->resize(ResizeType::Fill, 800, 600)->url();
// rs:fill:800:600
```

## Gravity

`Imsus\LaravelImgproxy\Enums\Gravity` — the imgproxy gravity type (`g:` option).

| Case | Value | Description |
| --- | --- | --- |
| `Center` | `'ce'` | Center of the image. |
| `North` | `'no'` | North (top edge). |
| `South` | `'so'` | South (bottom edge). |
| `East` | `'ea'` | East (right edge). |
| `West` | `'we'` | West (left edge). |
| `NorthWest` | `'nowe'` | North-west (top-left corner). |
| `NorthEast` | `'noea'` | North-east (top-right corner). |
| `SouthWest` | `'sowe'` | South-west (bottom-left corner). |
| `SouthEast` | `'soea'` | South-east (bottom-right corner). |
| `Smart` | `'sm'` | Smart gravity: libvips detects the most interesting section. |

Used by: [`gravity()`](/reference/api#crop--gravity), [`crop()`](/reference/api#crop--gravity), [`resizeWithGravity()`](/reference/api#resize), [`extend()`](/reference/api#transform), [`extendAspectRatio()`](/reference/api#transform), and the `gravity` preset key.

::: warning Smart gravity
`Smart` is not supported by `extend()` and `extendAspectRatio()`. Passing it throws `InvalidArgumentException`.
:::

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

$imgproxy->gravity(Gravity::Smart)->url();
// g:sm

$imgproxy->crop(0.5, 0.5, Gravity::North)->url();
// c:0.5:0.5:no
```

## Format

`Imsus\LaravelImgproxy\Enums\Format` — the imgproxy result image format (`f:` option).

| Case | Value | MIME type |
| --- | --- | --- |
| `Jpg` | `'jpg'` | `image/jpeg` |
| `Png` | `'png'` | `image/png` |
| `Webp` | `'webp'` | `image/webp` |
| `Avif` | `'avif'` | `image/avif` |
| `Gif` | `'gif'` | `image/gif` |
| `Ico` | `'ico'` | `image/x-icon` |
| `Svg` | `'svg'` | `image/svg+xml` |
| `Bmp` | `'bmp'` | `image/bmp` |
| `Tiff` | `'tiff'` | `image/tiff` |
| `Heic` | `'heic'` | `image/heic` |
| `Jxl` | `'jxl'` | `image/jxl` |

Used by: [`format()`](/reference/api#quality--format), [`formatQuality()`](/reference/api#quality--format), [`skipProcessing()`](/reference/api#quality--format), [`placeholder()`](/reference/api#effects), and the `format` preset key.

```php
use Imsus\LaravelImgproxy\Enums\Format;

$imgproxy->format(Format::Webp)->quality(85)->url();
// f:webp/q:85

$imgproxy->formatQuality(['webp' => 80, 'avif' => 60])->url();
// fq:webp:80:avif:60
```

## WatermarkPosition

`Imsus\LaravelImgproxy\Enums\WatermarkPosition` — the imgproxy watermark position (`wm:` option).

| Case | Value | Description |
| --- | --- | --- |
| `Center` | `'ce'` | Center of the image (the default). |
| `North` | `'no'` | North (top edge). |
| `South` | `'so'` | South (bottom edge). |
| `East` | `'ea'` | East (right edge). |
| `West` | `'we'` | West (left edge). |
| `NorthWest` | `'nowe'` | North-west (top-left corner). |
| `NorthEast` | `'noea'` | North-east (top-right corner). |
| `SouthWest` | `'sowe'` | South-west (bottom-left corner). |
| `SouthEast` | `'soea'` | South-east (bottom-right corner). |
| `Repeat` | `'re'` | Repeat and tile the watermark to fill the entire image. |

Used by: [`watermark()`](/reference/api#background--watermark).

```php
use Imsus\LaravelImgproxy\Enums\WatermarkPosition;

$imgproxy->watermark(0.5, WatermarkPosition::SouthEast, 10, 10)->url();
// wm:0.5:soea:10:10
```
