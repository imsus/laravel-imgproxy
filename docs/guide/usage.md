---
title: Basic Usage
description: Build signed imgproxy URLs with the fluent builder, options, presets, and storage disks.
---

# Basic Usage

## Facade and Helper

The `Imgproxy` facade and the `imgproxy()` helper both resolve the default instance and return a manager that starts builders:

```php
use Imsus\LaravelImgproxy\Imgproxy;

// Facade
$url = Imgproxy::url('https://example.com/image.jpg')
    ->width(640)
    ->url();

// Helper — equivalent
$url = imgproxy()->url('https://example.com/image.jpg')
    ->width(640)
    ->url();
```

Named instances are accessed via the manager:

```php
$url = Imgproxy::instance('staging')
    ->url('https://example.com/image.jpg')
    ->width(640)
    ->url();
```

## Building URLs

Start with `->url($source)`, chain option methods, and terminate with `->url()` or `__toString()`:

```php
use Imsus\LaravelImgproxy\Imgproxy;
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\ResizeType;

$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->quality(80)
    ->format(Format::Webp)
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/q:80/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

## Signing

With a key and salt configured, the `unsafe` slot is replaced by the HMAC-SHA256 signature:

```php
$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->url();

// https://imgproxy.example.com/7Fu-sZuoCXRc1LXWhM687mlhsd2SFxXBpiFjJk6vakw/rs:fill:300:300/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The signature covers the exact path emitted, so encoding choice and signing are computed together. `signature_size` truncates the signature to match the server's `IMGPROXY_SIGNATURE_SIZE`. An empty key or salt disables signing entirely.

Generate a fresh key/salt pair with the [imgproxy:key](/guide/installation) command.

See [Security](/guide/security) for details on signing behaviour and key management.

## Options

Every imgproxy v4 processing option has one typed, validating method. Options are appended in call order, and invalid values throw `InvalidArgumentException`.

Available methods, grouped by concern:

- **Resize** — `resize()`, `size()`, `resizingType()`, `width()`, `height()`, `minWidth()`, `minHeight()`, `zoom()`
- **Crop & gravity** — `crop()`, `trim()`, `padding()`, `gravity()`, `focusPoint()`
- **Quality & format** — `quality()`, `format()`, `formatQuality()`, `skipProcessing()`, `rawResponse()`
- **Effects** — `blur()`, `sharpen()`, `pixelate()`, `dpr()`
- **Transform** — `rotate()`, `autoRotate()`, `flip()`, `enlarge()`, `extend()`, `extendAspectRatio()`
- **Background & watermark** — `background()`, `watermark()`
- **Output** — `stripMetadata()`, `keepCopyright()`, `stripColorProfile()`, `preserveHdr()`, `enforceThumbnail()`, `returnAttachment()`, `cacheBuster()`, `expires()`, `filename()`, `imgproxyPreset()`
- **Security** — `maxSrcResolution()`, `maxSrcFileSize()`, `maxAnimationFrames()`, `maxAnimationFrameResolution()`, `maxResultDimension()`

For the full method signatures, see the [API Reference](/reference/api).

### Enums

Arguments accept the enum or its string value — `Gravity::Smart` and `'sm'` are interchangeable:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::url($source)->gravity(Gravity::Smart)->url();
Imgproxy::url($source)->gravity('sm')->url(); // equivalent
```

Available enums:

- `ResizeType` — `Fit`, `Fill`, `FillDown`, `Force`, `Auto`
- `Gravity` — compass points (`North`, `SouthEast`, …) plus `Smart`
- `Format` — `Jpg`, `Png`, `Webp`, `Avif`, `Gif`, `Ico`, `Svg`, `Bmp`, `Tiff`, `Heic`, `Jxl`
- `WatermarkPosition`

See [Enums Reference](/reference/enums) for the full list of values.

### Raw Escape Hatch

For imgproxy options not yet covered by a typed method, `raw()` appends a segment verbatim:

```php
Imgproxy::url($source)->raw('some:new:option')->url();
```

## Immutability

Every mutation returns a new builder. A base builder can be reused for several variants without accidental mutation:

```php
$base = Imgproxy::url('https://example.com/image.jpg')->quality(80);

$small = $base->width(320)->url();
$large = $base->width(1280)->url(); // quality:80 still applies; no width leak from $small
```

## Presets

Named option sets defined in config are applied from the builder:

```php
Imgproxy::url($source)->preset('thumb')->url();
```

A preset composes onto the builder before per-URL overrides, so options chained after it win:

```php
Imgproxy::url($source)->preset('thumb')->width(640)->url();
// rs:fill:300:300/w:640/...
```

Preset keys match the fluent method names; only single-value options are supported. Unknown presets and invalid values throw.

These are client-side presets — the package composes the options into the URL, so no imgproxy server configuration is required. imgproxy's own server-side presets are a separate mechanism, referenced with `imgproxyPreset()` (the `pr:` option).

## LQIP Placeholders

`placeholder()` returns a tiny blurred webp of the same source — `w:16`, `bl:8`, `f:webp` — for blur-up previews:

```php
$placeholder = Imgproxy::url('https://example.com/image.jpg')
    ->placeholder()
    ->url();

// https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The Blade components pair the placeholder with the full-size image automatically. See [Blade Components](/guide/blade-components).

## Storage Disks

Sources can come from a Laravel Storage disk. Public disks yield the disk's `url()`; private disks yield a pre-signed `temporaryUrl()`:

```php
use Illuminate\Support\Facades\Storage;

// Public disk -> url()
Storage::disk('public')->imgproxy('images/photo.jpg')
    ->width(800)
    ->format(Format::Webp)
    ->url();

// Private disk (S3) -> pre-signed temporaryUrl(), 5 minutes by default
Storage::disk('s3')->imgproxy('products/image.jpg', 3600)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
```

The builder has an equivalent `->disk($disk, $path)` method, with an optional expiration in seconds or as an absolute `DateTimeInterface`:

```php
imgproxy()->url('unused')->disk('s3', 'products/image.jpg', 3600)->width(800)->url();
```

See [Storage Integration](/guide/storage-integration) for the full macro and `->disk()` API.

## Next Steps

- [Resizing](/guide/resizing) — resize modes, dimensions, cropping, and gravity
- [Quality & Format](/guide/quality) — compression, output format, and skip processing
- [Effects](/guide/effects) — blur, sharpen, pixelate, and DPR
- [Blade Components](/guide/blade-components) — responsive `<img>` and `<picture>` tags
- [Security](/guide/security) — HMAC signing, key management, and URL safety
