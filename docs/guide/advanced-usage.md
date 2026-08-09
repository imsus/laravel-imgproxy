---
title: Advanced Usage
description: Multi-instance manager, presets, cache busting, server-side presets, security caps, and the raw escape hatch.
---

# Advanced Usage

Once you are comfortable building URLs, there are a handful of features worth knowing about: named instances, presets, cache busting, server-side presets, per-URL security caps, and the raw escape hatch.

## The Multi-Instance Manager

Most applications need a single imgproxy server, but some need several — staging versus production, or separate servers with different credentials. The package supports any number of named instances, each with its own server, credentials, and encoding.

To target a non-default instance, call `Imgproxy::instance()`:

```php
$url = Imgproxy::instance('staging')
    ->url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
```

Instances are defined in `config/laravel-imgproxy.php`:

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
        'signature_size' => 16,
        'encoding' => 'base64',
    ],
],
```

The `default` key at the top of the config file determines which instance is used when no name is passed to `Imgproxy::instance()` or `imgproxy()`.

## Named Presets

Presets are reusable sets of processing options defined in config and shared across instances. They compose onto the builder before per-URL overrides.

### Defining Presets

Define presets in `config/laravel-imgproxy.php` under the `presets` key. Each preset maps a fluent method name to its value:

```php
'presets' => [
    'thumb' => [
        'resize' => 'fill',
        'width' => 300,
        'height' => 300,
    ],

    'hero' => [
        'resize' => 'fill',
        'width' => 1200,
        'height' => 600,
        'quality' => 85,
    ],
],
```

### Applying Presets

Call `preset()` with the key name. Options chained after the preset override it:

```php
// Preset sets width:300; override sets width:640
$url = Imgproxy::url($source)
    ->preset('thumb')
    ->width(640)
    ->url();
// rs:fill:300:300/w:640/...
```

### How Preset Composition Works

Presets are expanded into their individual option segments and appended to the URL in order. Options chained after `preset()` land later in the segment chain, so they override earlier values — imgproxy processes options left to right, and the last one wins:

```php
$url = Imgproxy::url($source)
    ->preset('hero')        // rs:fill:1200:600/q:85
    ->quality(90)           // overrides q:85 with q:90
    ->url();
// .../rs:fill:1200:600/q:85/q:90/...
```

### Rules and Constraints

- **Preset keys must match fluent method names.** The key `resize` maps to `resize()`, `width` maps to `width()`, and so on.
- **Only single-value options are supported.** A preset entry like `'width' => 300` works; variadic options like `raw()` cannot be expressed in a preset.
- **Unknown presets throw.** Calling `->preset('nonexistent')` throws `InvalidArgumentException`.
- **Invalid values throw.** A preset with an invalid resize type or out-of-range quality triggers the same validation as a direct method call.

## Cache Busting

Append a version string to invalidate CDN, proxy, and browser caches. The buster becomes part of the URL path, so changing it forces a fresh fetch:

```php
Imgproxy::url($source)->cacheBuster('v2')->url();
// cb:v2
```

This is commonly tied to a file's updated timestamp or a content hash.

## Expiration

Set a Unix timestamp after which imgproxy returns 404. Pass `0` to disable expiration:

```php
// Expires in 1 hour
Imgproxy::url($source)->expires(time() + 3600)->url();
// exp:1723228800

// No expiration
Imgproxy::url($source)->expires(0)->url();
// exp:0
```

## Filename

Set the filename in the `Content-Disposition` header for downloads:

```php
Imgproxy::url($source)->filename('photo.jpg')->url();
// fn:photo.jpg
```

When the filename is already URL-safe base64 encoded, pass `encoded: true`:

```php
Imgproxy::url($source)->filename($encodedName, encoded: true)->url();
// fn:<base64>:1
```

## Return Attachment

Force the browser to download the image instead of displaying it inline:

```php
Imgproxy::url($source)->returnAttachment(true)->url();
// att:1
```

## Server-Side Presets

imgproxy itself supports presets defined on the server (via `IMGPROXY_PRESETS` / `IMGPROXY_PRESETS_PATH`). Reference them with `imgproxyPreset()`, which emits the `pr:` segment:

```php
Imgproxy::url($source)->imgproxyPreset('blog-cover')->url();
// pr:blog-cover
```

Pass multiple server-side preset names to apply them in order:

```php
Imgproxy::url($source)->imgproxyPreset('blog-cover', 'sharpen')->url();
// pr:blog-cover:sharpen
```

::: warning
Server-side presets are a separate mechanism from the client-side presets defined in `config/laravel-imgproxy.php`. If the server does not have the preset registered, imgproxy responds with `500`.
:::

## The `raw()` Escape Hatch

Append any imgproxy processing segment verbatim, without validation. Use this for options not yet covered by a typed method:

```php
Imgproxy::url($source)->raw('some:new:option')->url();
// some:new:option
```

The segment is appended in call order, just like any other method:

```php
$url = Imgproxy::url($source)
    ->width(800)
    ->raw('some:new:option')
    ->quality(80)
    ->url();
// w:800/some:new:option/q:80
```

::: tip
Prefer typed methods when they are available — they validate inputs and catch errors early. `raw()` skips all validation.
:::

## Security Caps

The imgproxy server enforces limits on source resolution, file size, and animation complexity. You can tighten these per-URL when the server has security options enabled:

```php
// Max source resolution: 5 megapixels
Imgproxy::url($source)->maxSrcResolution(5)->url();
// msr:5

// Max source file size: 10 MB
Imgproxy::url($source)->maxSrcFileSize(10485760)->url();
// msfs:10485760

// Max animation frames: 50
Imgproxy::url($source)->maxAnimationFrames(50)->url();
// maf:50

// Max animation frame resolution: 2 megapixels
Imgproxy::url($source)->maxAnimationFrameResolution(2)->url();
// mafr:2

// Max result dimension: 4000px
Imgproxy::url($source)->maxResultDimension(4000)->url();
// mrd:4000
```

These cap what the server is willing to process for a given request. They do not replace the server's own security configuration — they let you tighten limits per-URL.

See also: [Usage](/guide/usage), [Security](/guide/security)
