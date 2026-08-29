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
$url = Imgproxy::image('https://example.com/image.jpg')
    ->width(640)
    ->url();

// Helper — equivalent
$url = imgproxy()->image('https://example.com/image.jpg')
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

To express the source kind at the entry point, the manager also exposes `fromStorage()`, `fromPath()`, and `fromUrl()`:

```php
Imgproxy::fromStorage('images/photo.jpg', 'public')->width(640)->url(); // disk source
Imgproxy::fromPath('https://example.com/image.jpg')->width(640)->url(); // path source
Imgproxy::fromUrl('https://example.com/image.jpg')->width(640)->url();  // URL source
```

`fromStorage()` mirrors the Storage macro (source-subject-first: path, then disk) and resolves public disks to their `url()` and private disks to a pre-signed `temporaryUrl()`. `fromPath()` and `fromUrl()` are explicit forms of the default `image()` entry.

## Building URLs

Start every URL with `->image($source)`. Chain as many methods as you need and finish with `->url()` or `__toString()`:

```php
use Imsus\LaravelImgproxy\Imgproxy;

$url = Imgproxy::image('https://example.com/image.jpg')
    ->cover(300, 300)
    ->quality(80)
    ->toWebp()
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/q:80/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

Notice how the source URL is encoded and the processing options are appended as path segments. The builder has **two API layers**: high-level *intent methods* (`cover`, `fit`, `orient`, `toWebp`, `storePublicly`, …) that say what you want in domain terms and compile to option segments, and the typed *processing-option* layer (`resize()`, `crop()`, `gravity()`, `format()`, …) that maps one-to-one to imgproxy's options. Because the builder is immutable, you can chain options in any order without worrying about mutating a shared instance — more on that in a moment.

## Intent Methods <Badge type="tip" text="New in v2.2.0" />

The intent methods express the desired outcome in domain terms and compile down to the existing processing-option segments. They are the documented headline; drop down to the processing-option methods when you need precise wire-level control.

### Cover & Fit

```php
use Imsus\LaravelImgproxy\Imgproxy;
use Imsus\LaravelImgproxy\Enums\Gravity;

// Crop to fill a 800×600 box, anchoring the crop at the top edge
$url = Imgproxy::image($source)->cover(800, 600, Gravity::North)->url();
// rs:fill:800:600/g:no

// Fit within a 800×600 box (keeps the aspect ratio, never upscales)
$url = Imgproxy::image($source)->fit(800, 600)->url();
// rs:fit:800:600
```

Like imgproxy's default, neither method enlarges the source; chain `enlarge()` when you want to allow upscaling.

### Orientation & Flips

```php
Imgproxy::image($source)->orient()->url();          // ar:1 (EXIF auto-rotate)
Imgproxy::image($source)->flipVertically()->url();  // fl:0:1
Imgproxy::image($source)->flipHorizontally()->url(); // fl:1:0
```

### Format Shortcuts

```php
Imgproxy::image($source)->toWebp()->url(); // f:webp
Imgproxy::image($source)->toJpg()->url();  // f:jpg
Imgproxy::image($source)->toPng()->url();  // f:png
Imgproxy::image($source)->toAvif()->url(); // f:avif
```

### Optimize

A single call that defaults to WebP at quality 70 — the same convention as Laravel's `Image::optimize()`:

```php
Imgproxy::image($source)->optimize()->url();                 // f:webp/q:70
Imgproxy::image($source)->optimize('avif', 80)->url();       // f:avif/q:80
Imgproxy::image($source)->optimize(quality: 85)->url();      // f:webp/q:85
```

### Conditional Application

The builder is `Conditionable`, so `when()` and `unless()` let you build variants from one base — useful for keeping a placeholder and a full-size image consistent:

```php
$base = Imgproxy::image($source)->cover(400, 400);

$placeholder = $base->when($isPlaceholder, fn ($builder) => $builder->width(16)->blur(8)->toWebp());
$full = $base->when(! $isPlaceholder, fn ($builder) => $builder->quality(85));
```

### Store Publicly

`storePublicly()` is `toStorage()` with `['visibility' => 'public']` already applied, so you don't have to remember the options array:

```php
$image = Imgproxy::image($source)
    ->width(800)
    ->toWebp()
    ->storePublicly('s3', 'processed/photo.webp');
```

See [Materializing Processed Images](/guide/storage-integration) for the full `toStorage()` API.

## Signing

With a key and salt configured, the `unsafe` slot is automatically replaced by an HMAC-SHA256 signature:

```php
$url = Imgproxy::image('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->url();

// https://imgproxy.example.com/7Fu-sZuoCXRc1LXWhM687mlhsd2SFxXBpiFjJk6vakw/rs:fill:300:300/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The signature covers the exact path that is emitted, so encoding choice and signing are always computed together — you never have to coordinate them by hand. The `signature_size` config value truncates the signature to match your server's `IMGPROXY_SIGNATURE_SIZE`, and an empty key or salt disables signing entirely.

To generate a fresh key and salt pair, use the [imgproxy:key](/guide/installation) command. If you want to understand the details of how signing works, see the [Security](/guide/security) documentation.

## Processing Options

Every imgproxy v4 processing option has one typed, validating method. This is the precise wire-level layer — the escape hatch beneath the intent methods above. Options are appended in call order, and invalid values throw `InvalidArgumentException`.

Available methods, grouped by concern:

- **Resize** — `resize()`, `resizeWithGravity()`, `width()`, `height()`, `minWidth()`, `minHeight()`, `zoom()`
- **Crop & gravity** — `crop()`, `trim()`, `padding()`, `gravity()`, `focusPoint()`
- **Quality & format** — `quality()`, `format()`, `formatQuality()`, `skipProcessing()`, `raw()`
- **Effects** — `blur()`, `sharpen()`, `pixelate()`, `dpr()`
- **Transform** — `rotate()`, `autoRotate()`, `flip()`, `enlarge()`, `extend()`, `extendAspectRatio()`
- **Background & watermark** — `background()`, `watermark()`
- **Output** — `stripMetadata()`, `keepCopyright()`, `stripColorProfile()`, `preserveHDR()`, `enforceThumbnail()`, `returnAttachment()`, `cacheBuster()`, `expires()`, `filename()`, `preset()`
- **Security** — `maxSourceResolution()`, `maxSourceFileSize()`, `maxAnimationFrames()`, `maxAnimationFrameResolution()`, `maxResultDimension()`

For the full method signatures, see the [API Reference](/reference/api).

### Enums

Typed arguments accept the enum or its string value interchangeably — `Gravity::Smart` and `'sm'` produce identical results:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::image($source)->gravity(Gravity::Smart)->url();
Imgproxy::image($source)->gravity('sm')->url(); // equivalent
```

The package provides four enums:

- `ResizeType` — `Fit`, `Fill`, `FillDown`, `Force`, `Auto`
- `Gravity` — compass points (`North`, `SouthEast`, …) plus `Smart`
- `Format` — `Jpg`, `Png`, `Webp`, `Avif`, `Gif`, `Ico`, `Svg`, `Bmp`, `Tiff`, `Heic`, `Jxl`
- `WatermarkPosition`

See the [Enums Reference](/reference/enums) for the full list of values.

### The Raw Escape Hatch

imgproxy moves quickly, and occasionally a new option appears before this package has a typed method for it. When that happens, `withOption()` lets you append any segment verbatim:

```php
Imgproxy::image($source)->withOption('some:new:option')->url();
```

Prefer typed methods when they are available — they validate your input and catch errors early — but `withOption()` guarantees you are never blocked by the package.

## Immutability

Every mutation returns a **new** builder. This means a base builder can be reused for several variants without accidental mutation:

```php
$base = Imgproxy::image('https://example.com/image.jpg')->quality(80);

$small = $base->width(320)->url();
$large = $base->width(1280)->url(); // quality:80 still applies; no width leak from $small
```

This is a small design decision that pays off constantly in real applications — for example, when you want to render several image sizes from a single set of options.

## Presets

Named option sets defined in your config file can be applied with `applyPreset()`:

```php
Imgproxy::image($source)->applyPreset('thumb')->url();
```

A preset composes onto the builder before any options chained after it, so per-URL overrides win:

```php
Imgproxy::image($source)->applyPreset('thumb')->width(640)->url();
// rs:fill:300:300/w:640/...
```

Preset keys match the fluent method names, and only single-value options are supported. Unknown presets and invalid values throw.

::: tip Client-side vs. server-side presets
These presets are **client-side** — the package composes the options into the URL, so no imgproxy server configuration is required. imgproxy's own server-side presets are a separate mechanism, referenced with `preset()` (the `pr:` option).
:::

## LQIP Placeholders

For blur-up previews, `placeholder()` returns a tiny blurred webp of the same source — `w:16`, `bl:8`, `f:webp`:

```php
$placeholder = Imgproxy::image('https://example.com/image.jpg')
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
    ->toWebp()
    ->url();

// Private disk (S3) -> pre-signed temporaryUrl(), 5 minutes by default
Storage::disk('s3')->imgproxy('products/image.jpg', 3600)
    ->fit(800, 600)
    ->url();
```

At the facade entry, `fromStorage()` mirrors the macro (source-subject-first: path, then disk) and hands you the same builder:

```php
Imgproxy::fromStorage('images/photo.jpg', 'public')->width(800)->url();
Imgproxy::fromStorage('products/image.jpg', 's3')->fit(800, 600)->url(); // pre-signed, 5 minutes by default
```

The builder has an equivalent `->disk($disk, $path)` method, with an optional expiration in seconds or as an absolute `DateTimeInterface`:

```php
imgproxy()->image('unused')->disk('s3', 'products/image.jpg', 3600)->width(800)->url();
```

See the [Storage Integration](/guide/storage-integration) documentation for the full macro and `->disk()` API.

## Next Steps

- [Resizing](/guide/resizing) — resize modes, dimensions, cropping, and gravity
- [Quality & Format](/guide/quality) — compression, output format, and skip processing
- [Effects](/guide/effects) — blur, sharpen, pixelate, and DPR
- [Blade Components](/guide/blade-components) — responsive `<img>` and `<picture>` tags
- [Security](/guide/security) — HMAC signing, key management, and URL safety
