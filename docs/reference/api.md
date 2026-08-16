---
title: API Reference
description: Complete reference for the Imgproxy facade, imgproxy() helper, Builder methods, enums, Blade components, Storage macro, and Artisan commands.
---

# API Reference

This page is the complete API surface of the package. If you are looking for guidance on how to use these methods, start with the [Basic Usage](/guide/usage) documentation instead.

## The `imgproxy()` Helper

```php
function imgproxy(): Manager
```

Returns the [Manager](#manager) instance, a terse alternative to the facade. Defined globally in `src/helpers.php`.

```php
imgproxy()->image('https://example.com/image.jpg')->width(800)->url();
```

## The `Imgproxy` Facade

`Imsus\LaravelImgproxy\Imgproxy` proxies to the `Manager` singleton. It is deliberately not `final`, so applications can mock it with the standard `Imgproxy::shouldReceive(...)` pattern.

| Method | Signature | Description |
| --- | --- | --- |
| `image` | `image(string $source, ?string $instance = null): Builder` | Build a URL for the given source on the default (or named) instance. |
| `instance` | `instance(?string $name = null): Instance` | Resolve a named imgproxy instance. |
| `defaultInstance` | `defaultInstance(): string` | The name of the default imgproxy instance. |

## Manager

`Imsus\LaravelImgproxy\Manager` resolves imgproxy instances from the published config file.

```php
Imgproxy::instance('staging');           // resolve a named instance
Imgproxy::image('https://...');            // build on the default instance
Imgproxy::image('https://...', 'staging'); // build on a named instance
```

## Builder

`Imsus\LaravelImgproxy\Builder` is the immutable fluent builder. Every option method returns a **new** instance; `url()` and `__toString()` are terminal.

### Construction & Terminal

```php
public function __construct(
    string $baseUrl,
    string $source,
    string $encoding = 'base64',
    array $segments = [],
    ?string $key = null,
    ?string $salt = null,
    ?int $signatureSize = null,
    array $presets = [],
)
```

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `__construct` | `__construct(string $baseUrl, string $source, string $encoding = 'base64', array $segments = [], ?string $key = null, ?string $salt = null, ?int $signatureSize = null, array $presets = [])` | — | Create a builder with the given base URL, source, encoding, signing credentials, and preset option sets. |
| `url` | `url(): string` | — | The full imgproxy URL. |
| `__toString` | `__toString(): string` | — | Alias for `url()`. Works in string contexts. |
| `sourceEncoding` | `sourceEncoding(string $encoding): self` | — | Return a copy with a different source encoding (`base64` or `plain`). |
| `disk` | `disk(string $disk, string $path, int\|DateTimeInterface\|null $expiration = null): self` | — | Set the source to a file on a Storage disk. Public disks use `url()`; private disks use a pre-signed `temporaryUrl()`. |
| `toStorage` <Badge type="tip" text="v2.1.0" /> | `toStorage(string $disk, string $path, array $options = []): StoredImage` | — | Fetch the processed image from imgproxy and write it to a Storage disk, streaming the response body. Existing files are overwritten. Returns a [StoredImage](#storedimage). |

### Resize

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `resize` | `resize(ResizeType\|string $type, ?int $width = null, ?int $height = null, bool $enlarge = false, bool $extend = false): self` | `rs:` | Resize the image with a type, dimensions, and optional enlarge/extend flags. |
| `resizeWithGravity` | `resizeWithGravity(?int $width = null, ?int $height = null, bool $enlarge = false, bool $extend = false, Gravity\|string\|null $gravity = null): self` | `s:` | Set width, height, enlarge, extend, and gravity in one option. |
| `width` | `width(int $width): self` | `w:` | Set the width of the resulting image. `0` auto-calculates from height and aspect ratio. |
| `height` | `height(int $height): self` | `h:` | Set the height of the resulting image. `0` auto-calculates from width and aspect ratio. |
| `minWidth` | `minWidth(int $width): self` | `mw:` | Set the minimum width of the resulting image. |
| `minHeight` | `minHeight(int $height): self` | `mh:` | Set the minimum height of the resulting image. |
| `zoom` | `zoom(int\|float $x, int\|float\|null $y = null): self` | `z:` | Multiply image dimensions by the given factors. Unlike `dpr`, does not affect gravity offsets. |

### Crop & Gravity

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `crop` | `crop(int\|float $width, int\|float $height, Gravity\|string\|null $gravity = null): self` | `c:` | Define an area to crop before resize. Values below 1 are relative; `0` uses the full dimension. |
| `trim` | `trim(float $threshold, ?string $color = null, bool $equalHorizontal = false, bool $equalVertical = false): self` | `t:` | Remove the surrounding background. The color is 3 or 6 digit hex. |
| `padding` | `padding(int $top, ?int $right = null, ?int $bottom = null, ?int $left = null): self` | `pd:` | Add padding around the image using CSS-style syntax (sides default as in CSS). |
| `gravity` | `gravity(Gravity\|string $gravity, int\|float $xOffset = 0, int\|float $yOffset = 0): self` | `g:` | Set the gravity used when imgproxy cuts parts of the image. Offsets emitted only when non-zero. |
| `focusPoint` | `focusPoint(float $x, float $y): self` | `g:fp:` | Set the gravity focus point. Offsets between 0 and 1 (left/right for x, top/bottom for y). |

### Quality & Format

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `quality` | `quality(int $quality): self` | `q:` | Set the quality of the resulting image (0–100). `0` falls back to the server default. |
| `format` | `format(Format\|string $format): self` | `f:` | Set the resulting image format. |
| `formatQuality` | `formatQuality(array $qualities): self` | `fq:` | Redefine quality for specific output formats. Keys are format values (e.g. `'webp'`), values are 0–100. |
| `skipProcessing` | `skipProcessing(Format\|string ...$formats): self` | `skp:` | Skip processing for the given source formats. |
| `raw` | `raw(): self` | `raw:` | Respond with the raw unprocessed source image. |
| `withoutRaw` | `withoutRaw(): self` | `raw:` | Disable raw response. |

### Effects

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `blur` | `blur(int\|float $sigma): self` | `bl:` | Apply a Gaussian blur filter with the given sigma. |
| `sharpen` | `sharpen(int\|float $sigma): self` | `sh:` | Apply the sharpen filter with the given sigma. |
| `pixelate` | `pixelate(int $size): self` | `pix:` | Apply the pixelate filter with the given pixel size. |
| `dpr` | `dpr(int\|float $dpr): self` | `dpr:` | Multiply image dimensions for HiDPI (Retina) displays. Affects gravity offsets and paddings. |
| `placeholder` | `placeholder(): self` | `w:16`, `bl:8`, `f:webp` | Return a tiny blurred webp of the same source for LQIP placeholders. Chains `width(16)`, `blur(8)`, and `format(Format::Webp)`. |

### Transform

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `rotate` | `rotate(int $angle): self` | `rot:` | Rotate the image by the given angle (must be a non-negative multiple of 90). |
| `autoRotate` | `autoRotate(): self` | `ar:` | Automatically rotate based on the EXIF orientation. |
| `withoutAutoRotate` | `withoutAutoRotate(): self` | `ar:` | Disable automatic rotation. |
| `flip` | `flip(bool $horizontal = false, bool $vertical = false): self` | `fl:` | Flip the image along the horizontal and/or vertical axes. |
| `enlarge` | `enlarge(): self` | `el:` | Enlarge the image when it is smaller than the given size. |
| `withoutEnlarge` | `withoutEnlarge(): self` | `el:` | Disable enlargement. |
| `extend` | `extend(Gravity\|string\|null $gravity = null): self` | `ex:` | Extend the image when it is smaller than the given size. Smart gravity not supported. |
| `withoutExtend` | `withoutExtend(): self` | `ex:` | Disable extension. |
| `extendAspectRatio` | `extendAspectRatio(Gravity\|string\|null $gravity = null): self` | `exar:` | Extend the image to the requested aspect ratio. Smart gravity not supported. |
| `withoutExtendAspectRatio` | `withoutExtendAspectRatio(): self` | `exar:` | Disable aspect ratio extension. |

### Background & Watermark

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `background` | `background(string $color): self` | `bg:` | Fill the resulting image background with a 3 or 6 digit hex color. |
| `watermark` | `watermark(int\|float $opacity, WatermarkPosition\|string\|null $position = null, int\|float $xOffset = 0, int\|float $yOffset = 0, int\|float $scale = 0): self` | `wm:` | Place a watermark on the processed image. Opacity is 0–1 (exclusive 0). Scale of 0 leaves the watermark size unchanged. |

### Output

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `stripMetadata` | `stripMetadata(): self` | `sm:` | Strip the output image metadata (EXIF, IPTC, etc.). |
| `withoutMetadata` | `withoutMetadata(): self` | `sm:` | Keep output image metadata. |
| `keepCopyright` | `keepCopyright(): self` | `kcr:` | Keep the copyright info while stripping metadata. |
| `withoutCopyright` | `withoutCopyright(): self` | `kcr:` | Strip copyright info along with metadata. |
| `stripColorProfile` | `stripColorProfile(): self` | `scp:` | Transform the embedded color profile to sRGB and remove it. |
| `withoutColorProfile` | `withoutColorProfile(): self` | `scp:` | Keep the embedded color profile. |
| `preserveHDR` | `preserveHDR(): self` | `ph:` | Keep high bit images high bit instead of downscaling to 8 bit. |
| `enforceThumbnail` | `enforceThumbnail(): self` | `eth:` | Always use the embedded thumbnail of the source image when available. |
| `returnAttachment` | `returnAttachment(): self` | `att:` | Return the processed image as an attachment instead of inline. |
| `cacheBuster` | `cacheBuster(string $buster): self` | `cb:` | Add a cache buster to bypass CDN, proxy, and browser caches. |
| `expires` | `expires(int $timestamp): self` | `exp:` | Set the unix timestamp after which imgproxy returns 404. `0` disables expiration. |
| `filename` | `filename(string $filename, bool $encoded = false): self` | `fn:` | Set the filename for the `Content-Disposition` header. Pass `true` for `$encoded` if the filename is already URL-safe base64. |
| `preset` | `preset(string $name, string ...$more): self` | `pr:` | Apply server-side presets configured on the imgproxy instance. The server must have the preset registered. |

### Presets

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `applyPreset` | `applyPreset(string $name): self` | — | Compose a named preset from config onto the builder. Options are appended before any options chained after this call, so per-URL overrides win. |

### Security

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `maxSourceResolution` | `maxSourceResolution(int\|float $megapixels): self` | `msr:` | Redefine the maximum source image resolution in megapixels. Requires security options on the server. |
| `maxSourceFileSize` | `maxSourceFileSize(int $bytes): self` | `msfs:` | Redefine the maximum source image file size in bytes. |
| `maxAnimationFrames` | `maxAnimationFrames(int $frames): self` | `maf:` | Redefine the maximum number of animation frames. |
| `maxAnimationFrameResolution` | `maxAnimationFrameResolution(int\|float $megapixels): self` | `mafr:` | Redefine the maximum animation frame resolution in megapixels. |
| `maxResultDimension` | `maxResultDimension(int $pixels): self` | `mrd:` | Redefine the maximum dimension of the resulting image in pixels. |

### Escape Hatch

| Method | Signature | imgproxy segment | Description |
| --- | --- | --- | --- |
| `withOption` | `withOption(string $segment): self` | *(verbatim)* | Append a processing option segment verbatim, without validation. Use for imgproxy options not yet covered by a typed method. |

## StoredImage <Badge type="tip" text="New in v2.1.0" />

`Imsus\LaravelImgproxy\StoredImage` is the representation of a processed image fetched from imgproxy and written to a Storage disk by `Builder::toStorage()`. It is immutable and holds only the disk name and path; the disk adapter and URLs resolve lazily.

| Method | Signature | Description |
| --- | --- | --- |
| `disk` | `disk(): string` | The destination disk name. |
| `path` | `path(): string` | The path of the stored image on the destination disk. |
| `name` | `name(): string` | The file name of the stored image. |
| `url` | `url(int\|DateTimeInterface\|null $expiration = null): string` | A URL for the stored image: the plain object URL on public disks, a pre-signed `temporaryUrl()` on private ones (5 minutes by default). |
| `adapter` | `adapter(): FilesystemAdapter` | The destination disk adapter, for advanced operations. |
| `__toString` | `__toString(): string` | Alias for `url()`. Works in string contexts. |

See [Storage Integration](/guide/storage-integration#materializing-processed-images) for usage.

## Enums

See the [Enums Reference](/reference/enums) for the complete list of cases and their imgproxy segment mappings.

| Enum | Namespace | Options |
| --- | --- | --- |
| `ResizeType` | `Imsus\LaravelImgproxy\Enums\ResizeType` | `resize()`, preset `resize` |
| `Gravity` | `Imsus\LaravelImgproxy\Enums\Gravity` | `gravity()`, `crop()`, `resizeWithGravity()`, `extend()`, `extendAspectRatio()` |
| `Format` | `Imsus\LaravelImgproxy\Enums\Format` | `format()`, `formatQuality()`, `skipProcessing()`, `placeholder()` |
| `WatermarkPosition` | `Imsus\LaravelImgproxy\Enums\WatermarkPosition` | `watermark()` |

All enum arguments accept the enum instance **or** its string backing value interchangeably:

```php
use Imsus\LaravelImgproxy\Enums\Gravity;

$imgproxy->gravity(Gravity::Smart);  // enum instance
$imgproxy->gravity('sm');            // string value — identical result
```

## Blade Components

### The `<x-imgproxy-img>` Component

Renders an `<img>` with a srcset built from width or DPR candidates, sizes, an LQIP placeholder, lazy loading, alt text, and class passthrough.

| Attribute | Type | Default | Description |
| --- | --- | --- | --- |
| `src` | `string` | `''` | Source image URL. Mutually exclusive with `disk` + `path`. |
| `disk` | `string\|null` | `null` | Storage disk name. Requires `path`. |
| `path` | `string\|null` | `null` | File path on the disk. Requires `disk`. |
| `preset` | `string\|null` | `null` | Named preset from config to compose before overrides. |
| `widths` | `array\|string\|null` | `null` | Srcset width candidates (array or comma-separated string). Mutually exclusive with `dprs`. |
| `dprs` | `array\|string\|null` | `null` | Srcset DPR candidates (array or comma-separated string). Mutually exclusive with `widths`. |
| `sizes` | `string\|null` | `null` | The `sizes` attribute for the `<img>`. |
| `placeholder` | `bool` | `false` | When true, `src` is a tiny blurred webp; the full image is reachable through the srcset. |
| `alt` | `string\|null` | `null` | Alt text for the `<img>`. |
| `loading` | `string` | `'lazy'` | Loading strategy (`lazy` or `eager`). |

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

### The `<x-imgproxy-picture>` Component

Renders a `<picture>` with one `<source>` per format and a fallback `<img>`. Every format except the last becomes a `<source>`; the last is the fallback image.

Inherits all `<x-imgproxy-img>` attributes, plus:

| Attribute | Type | Default | Description |
| --- | --- | --- | --- |
| `formats` | `array\|string\|null` | `['avif', 'webp', 'jpg']` | Output formats. The last is the fallback `<img>`. |

```blade
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    :widths="[640, 1280]"
    :formats="['avif', 'webp', 'jpg']"
    sizes="100vw"
    alt="A photo"
/>
```

## Storage Macro

Registered on `Illuminate\Filesystem\FilesystemAdapter` by the service provider:

```php
FilesystemAdapter::macro('imgproxy', function (string $path, int|DateTimeInterface|null $expiration = null): Builder
```

Public disks yield `url()`; private disks (those providing temporary URLs without explicit public visibility) yield a pre-signed `temporaryUrl()`.

```php
use Illuminate\Support\Facades\Storage;

// Public disk
Storage::disk('public')->imgproxy('images/photo.jpg')->width(800)->url();

// Private disk with 1-hour expiry
Storage::disk('s3')->imgproxy('products/image.jpg', 3600)->resize(ResizeType::Fill, 800, 600)->url();
```

## Configuration

Published config file: `config/laravel-imgproxy.php`.

### Instances

Each instance points at one imgproxy server. The `default` instance reads from environment variables.

```php
'instances' => [
    'default' => [
        'url' => env('IMGPROXY_URL'),             // Base URL, no trailing slash
        'key' => env('IMGPROXY_KEY'),             // Hex-encoded HMAC key, or null for unsigned URLs
        'salt' => env('IMGPROXY_SALT'),           // Hex-encoded HMAC salt
        'signature_size' => null,                 // Bytes to keep (1–32), null for full 32
        'encoding' => 'base64',                   // "base64" (URL-safe, default) or "plain" (percent-encoded)
    ],
],
```

### Presets

Named option sets shared across instances. Keys match the fluent method names; only single-value options are supported.

```php
'presets' => [
    'thumb' => [
        'resize' => 'fill',
        'width' => 300,
        'height' => 300,
    ],
],
```

## Artisan Commands

### `imgproxy:key`

Generates a fresh 32-byte hex key and salt pair for URL signing. Prints environment lines for you to copy into `.env`.

```bash
php artisan imgproxy:key

Generated a new imgproxy key and salt pair.

IMGPROXY_KEY=...
IMGPROXY_SALT=...
```

The command never writes to `.env` itself.

### `imgproxy:health`

Checks an instance's `/health` endpoint and exits non-zero when the instance is unreachable, unhealthy, or not configured.

```bash
php artisan imgproxy:health              # default instance
php artisan imgproxy:health --instance=staging
```
