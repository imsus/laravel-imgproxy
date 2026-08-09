---
title: Basic Usage
description: Build signed imgproxy URLs with the fluent builder, options, presets, and Storage disks.
---

# Basic Usage

## The Facade and Helper

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

If you have registered multiple instances, you can target a specific one:

```php
$url = Imgproxy::instance('staging')
    ->url('https://example.com/image.jpg')
    ->width(640)
    ->url();
```

## Building URLs

Every URL begins with `->url($source)`. You then chain as many option methods as you need and finish with `->url()` or `__toString()`:

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

Notice how the source URL is encoded and the processing options are appended as path segments. Because the builder is immutable, you can chain options in any order without worrying about mutating a shared instance — more on that in a moment.

## Signing

With a key and salt configured, the `unsafe` slot is automatically replaced by an HMAC-SHA256 signature:

```php
$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->url();

// https://imgproxy.example.com/7Fu-sZuoCXRc1LXWhM687mlhsd2SFxXBpiFjJk6vakw/rs:fill:300:300/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The signature covers the exact path that is emitted, so encoding choice and signing are always computed together — you never have to coordinate them by hand. The `signature_size` config value truncates the signature to match your server's `IMGPROXY_SIGNATURE_SIZE`, and an empty key or salt disables signing entirely.

To generate a fresh key and salt pair, use the [imgproxy:key](/guide/installation) command. If you want to understand the details of how signing works, see the [Security](/guide/security) documentation.

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

Typed arguments accept the enum or its string value interchangeably — `Gravity::Smart` and `'sm'` produce identical results:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::url($source)->gravity(Gravity::Smart)->url();
Imgproxy::url($source)->gravity('sm')->url(); // equivalent
```

The package provides four enums:

- `ResizeType` — `Fit`, `Fill`, `FillDown`, `Force`, `Auto`
- `Gravity` — compass points (`North`, `SouthEast`, …) plus `Smart`
- `Format` — `Jpg`, `Png`, `Webp`, `Avif`, `Gif`, `Ico`, `Svg`, `Bmp`, `Tiff`, `Heic`, `Jxl`
- `WatermarkPosition`

See the [Enums Reference](/reference/enums) for the full list of values.

### The Raw Escape Hatch

imgproxy moves quickly, and occasionally a new option appears before this package has a typed method for it. When that happens, `raw()` lets you append any segment verbatim:

```php
Imgproxy::url($source)->raw('some:new:option')->url();
```

Prefer typed methods when they are available — they validate your input and catch errors early — but `raw()` guarantees you are never blocked by the package.

## Immutability

Every mutation returns a **new** builder. This means a base builder can be reused for several variants without accidental mutation:

```php
$base = Imgproxy::url('https://example.com/image.jpg')->quality(80);

$small = $base->width(320)->url();
$large = $base->width(1280)->url(); // quality:80 still applies; no width leak from $small
```

This is a small design decision that pays off constantly in real applications — for example, when you want to render several image sizes from a single set of options.

## Presets

Named option sets defined in your config file can be applied with `preset()`:

```php
Imgproxy::url($source)->preset('thumb')->url();
```

A preset composes onto the builder before any options chained after it, so per-URL overrides win:

```php
Imgproxy::url($source)->preset('thumb')->width(640)->url();
// rs:fill:300:300/w:640/...
```

Preset keys match the fluent method names, and only single-value options are supported. Unknown presets and invalid values throw.

::: tip Client-side vs. server-side presets
These presets are **client-side** — the package composes the options into the URL, so no imgproxy server configuration is required. imgproxy's own server-side presets are a separate mechanism, referenced with `imgproxyPreset()` (the `pr:` option).
:::

## LQIP Placeholders

For blur-up previews, `placeholder()` returns a tiny blurred webp of the same source — `w:16`, `bl:8`, `f:webp`:

```php
$placeholder = Imgproxy::url('https://example.com/image.jpg')
    ->placeholder()
    ->url();

// https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The [Blade components](/guide/blade-components) pair this placeholder with the full-size image automatically.

## Storage Disks

Sources can come from any Laravel Storage disk. Public disks yield the disk's `url()`; private disks yield a pre-signed `temporaryUrl()`:

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

See the [Storage Integration](/guide/storage-integration) documentation for the full macro and `->disk()` API.

## Next Steps

- [Resizing](/guide/resizing) — resize modes, dimensions, cropping, and gravity
- [Quality & Format](/guide/quality) — compression, output format, and skip processing
- [Effects](/guide/effects) — blur, sharpen, pixelate, and DPR
- [Blade Components](/guide/blade-components) — responsive `<img>` and `<picture>` tags
- [Security](/guide/security) — HMAC signing, key management, and URL safety
