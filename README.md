<div align="center">
    <h1>Laravel imgproxy</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/v/imsus/laravel-imgproxy.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/php-v/imsus/laravel-imgproxy.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://badge.laravel.cloud/badge/imsus/laravel-imgproxy?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/imsus/laravel-imgproxy/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/dt/imsus/laravel-imgproxy.svg?style=flat-square" alt="Total Downloads"></a>
</p>

imgproxy integration for Laravel. Generate signed, processed image URLs with an immutable fluent builder, render responsive `<img>` and `<picture>` tags with LQIP placeholders, and build sources from Storage disks.

## Requirements

- PHP ^8.4
- Laravel 13
- An imgproxy server (v4 recommended; the option segments target the v4 processing docs)

## Installation

You can install the package via Composer:

```bash
composer require imsus/laravel-imgproxy
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="laravel-imgproxy"
```

The component templates publish to `resources/views/vendor/imgproxy` (`laravel-imgproxy-views` tag) and override the package defaults.

## Configuration

The default instance reads its connection details from the environment, so the package works after setting three variables:

```dotenv
IMGPROXY_URL=https://imgproxy.example.com
IMGPROXY_KEY=943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881
IMGPROXY_SALT=520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5
```

`IMGPROXY_KEY` and `IMGPROXY_SALT` are the hex-encoded values configured on the imgproxy server (`IMGPROXY_KEY` / `IMGPROXY_SALT`). When they are absent, URLs are generated unsigned with an `unsafe` signature slot — fine for local development, but configure them for anything exposed to the internet.

The published `config/laravel-imgproxy.php` supports multiple named instances, each with its own server, credentials, signature size, and encoding:

```php
'instances' => [
    'default' => [
        'url' => env('IMGPROXY_URL'),
        'key' => env('IMGPROXY_KEY'),
        'salt' => env('IMGPROXY_SALT'),
        'signature_size' => null,
        'encoding' => 'base64',
    ],

    'staging' => [
        'url' => 'https://imgproxy-staging.example.com',
        'key' => '...',
        'salt' => '...',
        'signature_size' => 16, // match IMGPROXY_SIGNATURE_SIZE on the server
        'encoding' => 'base64',
    ],
],
```

Per-instance options:

- `url` — base URL of the imgproxy server, without a trailing slash.
- `key` / `salt` — hex-encoded HMAC credentials; `null` generates unsigned URLs.
- `signature_size` — signature bytes to keep (1–32), matching the server's `IMGPROXY_SIGNATURE_SIZE`; `null` keeps the full digest.
- `encoding` — source encoding: `base64` (URL-safe, no padding, the default) or `plain` (percent-encoded source behind a `plain/` prefix).

## Usage

### Building URLs

The `Imgproxy` facade and the `imgproxy()` helper both resolve the default instance:

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

// The helper is equivalent:
$url = imgproxy()->url('https://example.com/image.jpg')->width(640)->url();
```

`url()` and `__toString()` return the full URL; either works as a terminal call.

### Signing

With a key and salt configured, the `unsafe` slot is replaced by the HMAC-SHA256 signature:

```php
$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->url();

// https://imgproxy.example.com/7Fu-sZuoCXRc1LXWhM687mlhsd2SFxXBpiFjJk6vakw/rs:fill:300:300/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The signature covers the exact path emitted, so encoding choice and signing are computed together. `signature_size` truncates the signature to match the server; an empty key or salt disables signing. The `imgproxy:key` command generates a fresh pair.

### Options

Every imgproxy v4 processing option has one typed, validating method. Options are appended in call order, and invalid values throw `InvalidArgumentException`:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

Imgproxy::url($source)
    ->resize(ResizeType::Fill, 800, 600, enlarge: true) // rs:fill:800:600:1
    ->gravity(Gravity::Smart)                            // g:sm
    ->crop(0.5, 0.5, Gravity::North)                    // c:0.5:0.5:no
    ->blur(1.5)                                         // bl:1.5
    ->sharpen(0.5)                                      // sh:0.5
    ->dpr(2)                                            // dpr:2
    ->background('#f8fafc')                             // bg:f8fafc
    ->watermark(0.5, WatermarkPosition::SouthEast, 10, 10) // wm:0.5:soea:10:10
    ->rotate(90)                                        // rot:90
    ->url();
```

Available methods, grouped by concern:

- **Resize** — `resize()`, `size()`, `resizingType()`, `width()`, `height()`, `minWidth()`, `minHeight()`, `zoom()`
- **Crop & gravity** — `crop()`, `trim()`, `padding()`, `gravity()`, `focusPoint()`
- **Quality & format** — `quality()`, `format()`, `formatQuality()`, `skipProcessing()`, `rawResponse()`
- **Effects** — `blur()`, `sharpen()`, `pixelate()`, `dpr()`
- **Transform** — `rotate()`, `autoRotate()`, `flip()`, `enlarge()`, `extend()`, `extendAspectRatio()`
- **Background & watermark** — `background()`, `watermark()`
- **Output** — `stripMetadata()`, `keepCopyright()`, `stripColorProfile()`, `preserveHdr()`, `enforceThumbnail()`, `returnAttachment()`, `cacheBuster()`, `expires()`, `filename()`, `imgproxyPreset()`
- **Security** — `maxSrcResolution()`, `maxSrcFileSize()`, `maxAnimationFrames()`, `maxAnimationFrameResolution()`, `maxResultDimension()`

Enum arguments accept the enum or its string value — `Gravity::Smart` and `'sm'` are interchangeable. The enums are `ResizeType` (`fit`, `fill`, `fill-down`, `force`, `auto`), `Gravity` (compass points plus `sm`), `Format` (jpg, png, webp, avif, gif, ico, svg, bmp, tiff, heic, jxl), and `WatermarkPosition`.

For options that are not yet covered by a typed method, `raw()` appends a segment verbatim, in order:

```php
Imgproxy::url($source)->raw('some:new:option')->url();
```

### Immutability

Every mutation returns a new builder, so a base builder can be reused for several variants without accidental mutation:

```php
$base = Imgproxy::url('https://example.com/image.jpg')->quality(80);

$small = $base->width(320)->url();
$large = $base->width(1280)->url(); // quality:80 still applies; no width leak from $small
```

### Presets

Named option sets are defined in config and shared across instances:

```php
'presets' => [
    'thumb' => [
        'resize' => 'fill',
        'width' => 300,
        'height' => 300,
    ],
],
```

A preset composes onto the builder before per-URL overrides, so options chained after it win:

```php
Imgproxy::url($source)->preset('thumb')->width(640)->url();
// rs:fill:300:300/w:640/...
```

Preset keys match the fluent method names; only single-value options are supported. Unknown presets and invalid values throw.

These are client-side presets: the package composes the options into the URL, so no imgproxy server configuration is required. imgproxy's own server-side presets (defined via `IMGPROXY_PRESETS` / `IMGPROXY_PRESETS_PATH` on the server) are a separate mechanism, referenced with `imgproxyPreset()` (the `pr:` option) — the server must have the preset registered or it responds `500`.

### LQIP placeholders

`placeholder()` returns a tiny blurred webp of the same source — `w:16`, `bl:8`, `f:webp` — for blur-up previews:

```php
$placeholder = Imgproxy::url('https://example.com/image.jpg')->placeholder()->url();

// https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The Blade components pair the placeholder with the full-size image automatically.

### Storage disks

Sources can come from a Storage disk. Public disks yield the disk's `url()`; private disks yield a pre-signed `temporaryUrl()`:

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

## Blade Components

### `<x-imgproxy-img>`

Renders an `<img>` with a srcset built from width or DPR candidates, sizes, an LQIP placeholder, lazy loading by default, alt text, and class passthrough:

```blade
<x-imgproxy-img
    src="https://example.com/image.jpg"
    :widths="[320, 640, 1280]"
    sizes="(min-width: 1024px) 50vw, 100vw"
    preset="thumb"
    placeholder
    alt="A photo"
    class="rounded shadow"
/>
```

```html
<img src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/..." srcset="https://imgproxy.example.com/unsafe/rs:fill:300:300/w:320/... 320w, ..." sizes="(min-width: 1024px) 50vw, 100vw" loading="lazy" alt="A photo" class="rounded shadow">
```

- `widths` and `dprs` build the srcset (`w` or `x` descriptors); they are mutually exclusive and accept an array or a comma-separated string.
- `preset` composes a named preset from config before per-URL overrides.
- `placeholder` swaps the `src` to a tiny blurred webp of the same source and keeps the full image reachable through the srcset.
- `loading` defaults to `lazy`; pass `loading="eager"` to opt out.
- `src` may be replaced with `disk` and `path` to build the source from a Storage disk, matching the builder's `->disk()` behavior.

### `<x-imgproxy-picture>`

Renders a `<picture>` with one `<source>` per format and a fallback `<img>`. Every format except the last becomes a source; the last is the fallback image (AVIF and WebP sources with a JPG fallback by default):

```blade
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    :widths="[640, 1280]"
    :formats="['avif', 'webp', 'jpg']"
    sizes="100vw"
    alt="A photo"
/>
```

```html
<picture>
    <source srcset="https://imgproxy.example.com/unsafe/f:avif/w:640/... 640w, ..." type="image/avif">
    <source srcset="https://imgproxy.example.com/unsafe/f:webp/w:640/... 640w, ..." type="image/webp">
    <img src="https://imgproxy.example.com/unsafe/f:jpg/..." loading="lazy" alt="A photo">
</picture>
```

The fallback `<img>` accepts the same attributes as `<x-imgproxy-img>`.

## Artisan Commands

### `imgproxy:key`

Generates a fresh 32-byte key and salt pair for URL signing, printed as environment lines ready for `.env`:

```bash
php artisan imgproxy:key

Generated a new imgproxy key and salt pair.

IMGPROXY_KEY=...
IMGPROXY_SALT=...
```

The command never writes `.env` itself.

### `imgproxy:health`

Checks an instance's `/health` endpoint and exits non-zero when the instance is unreachable, unhealthy, or not configured:

```bash
php artisan imgproxy:health          # default instance
php artisan imgproxy:health --instance=staging
```

## Testing Against a Real imgproxy

The test suite includes live checks against a real imgproxy running locally in Docker. They are gated behind three environment variables and skip when they are not set (CI never sets them):

```dotenv
IMGPROXY_URL=http://localhost:8081
IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000
IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000
```

The key and salt must match the local server's `IMGPROXY_KEY` / `IMGPROXY_SALT`. With the variables exported, the live tests run as part of `composer test`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently. Upgrading from 1.x? See [UPGRADING](UPGRADING.md).

## Contributing

Thank you for considering contributing to Laravel imgproxy! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Imam Susanto](https://github.com/imsus)
- [All Contributors](../../contributors)

## License

Laravel imgproxy is open-sourced software licensed under the [MIT license](LICENSE.md).
