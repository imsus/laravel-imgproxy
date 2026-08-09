# Upgrading from 1.x to 2.0.0

2.0.0 is a ground-up rewrite. There is no v1 API compatibility layer, so most v1 call sites need changes. The package now targets PHP ^8.4 and Laravel 13 only, and covers the imgproxy v4 option surface with short option segments.

## Namespace and class names

| 1.x | 2.0.0 |
| --- | --- |
| `Imsus\ImgProxy\ImgProxy` | `Imsus\LaravelImgproxy\Builder` |
| `Imsus\ImgProxy\Facades\ImgProxy` | `Imsus\LaravelImgproxy\Imgproxy` |
| `Imsus\ImgProxy\Enums\ResizeType` | `Imsus\LaravelImgproxy\Enums\ResizeType` |
| `Imsus\ImgProxy\Enums\OutputExtension` | `Imsus\LaravelImgproxy\Enums\Format` |
| `Imsus\ImgProxy\Enums\Gravity` | `Imsus\LaravelImgproxy\Enums\Gravity` |

`OutputExtension` is replaced by `Format`, whose values are the v4 format names (`jpg`, `webp`, `avif`, …). New enums cover the added surface: `Format` includes `ico`, `svg`, `bmp`, `tiff`, `heic`, and `jxl`; `WatermarkPosition` is new.

## Helper and facade

The helper no longer takes a source URL; it returns the manager, and the facade proxies to it:

```php
// 1.x
imgproxy('https://example.com/image.jpg')->setWidth(800)->build();

// 2.0.0
imgproxy()->url('https://example.com/image.jpg')->width(800)->url();
// or
Imgproxy::url('https://example.com/image.jpg')->width(800)->url();
```

## Builder methods

`build()` is replaced by `url()` (or `__toString()`). Setter methods are replaced by typed methods that emit imgproxy v4 short segments:

| 1.x | 2.0.0 |
| --- | --- |
| `setWidth(800)` | `width(800)` |
| `setHeight(600)` | `height(600)` |
| `setResizeType(ResizeType::FILL)` | `resize(ResizeType::Fill, $width, $height)` |
| `setExtension(OutputExtension::WEBP)` | `format(Format::Webp)` |
| `setQuality(85)` | `quality(85)` |
| `setBlur(2.0)` | `blur(2.0)` |
| `setSharpen(1.0)` | `sharpen(1.0)` |
| `setDpr(2)` | `dpr(2)` |

```php
// 1.x — long segments, plain source
// http://imgproxy.local/signature/width:800/height:600/plain/https://example.com/image.jpg@jpeg

// 2.0.0 — short v4 segments, URL-safe base64 source by default
Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 800, 600)
    ->format(Format::Webp)
    ->quality(85)
    ->url();
```

Two behaviors changed with the URL shape:

- The generated URL uses the v4 short option segments (`w:`, `rs:`, `f:`, …) regardless of the old `use_short_options` config flag; the flag is gone.
- The source is URL-safe base64 encoded by default instead of plain. Use `->encoding('plain')` or set `'encoding' => 'plain'` on an instance to restore readable URLs.

v1's `cover()`, `webp()`, and similar convenience aliases are gone; use `resize(ResizeType::Fill, …)` and `format(Format::Webp)`.

## Configuration

The config file moved from `config/imgproxy.php` to `config/laravel-imgproxy.php` (the publish tag `laravel-imgproxy-config` is unchanged). Re-publish and re-apply your settings:

| 1.x key | 2.0.0 key |
| --- | --- |
| `endpoint` / `IMGPROXY_ENDPOINT` | `instances.default.url` / `IMGPROXY_URL` |
| `key` / `IMGPROXY_KEY` | `instances.default.key` / `IMGPROXY_KEY` |
| `salt` / `IMGPROXY_SALT` | `instances.default.salt` / `IMGPROXY_SALT` |
| `default_source_url_mode` | `instances.default.encoding` (`base64` or `plain`) |
| `default_output_extension` | removed — call `->format()` per URL |
| `default_gravity` | removed — call `->gravity()` per URL |
| `use_short_options` | removed — short segments are always used |
| `fallback_url` | removed |

The new `instances` map supports multiple imgproxy servers, each with its own `url`, `key`, `salt`, `signature_size`, and `encoding`. The new `presets` section defines named option sets shared across instances.

## Blade components

The component names `<x-imgproxy-img>` and `<x-imgproxy-picture>` are unchanged, but the attributes changed. v1 took a single size and format:

```blade
{{-- 1.x --}}
<x-imgproxy-img src="https://example.com/image.jpg" :width="300" :height="200" resize-type="fill" format="webp" :quality="85" />
```

2.0.0 renders responsive srcsets and takes `widths` / `dprs`, `sizes`, `preset`, `placeholder`, and `disk` + `path`:

```blade
{{-- 2.0.0 --}}
<x-imgproxy-img
    src="https://example.com/image.jpg"
    :widths="[320, 640, 1280]"
    sizes="100vw"
    preset="thumb"
    placeholder
    alt="A photo"
/>
```

`width`, `height`, and `format` attributes are no longer supported; a single fixed size is `:widths="[300]"`. Republish the component views (`laravel-imgproxy-views` tag) if you previously published overrides.

## Storage macro

The macro name is unchanged, but the returned builder now terminates with `url()` instead of `build()`:

```php
// 1.x
Storage::disk('s3')->imgproxy('products/image.jpg')->setWidth(800)->build();

// 2.0.0
Storage::disk('s3')->imgproxy('products/image.jpg')->width(800)->url();
```

Public/private detection is unchanged in spirit — public disks yield `url()`, private disks a pre-signed `temporaryUrl()` — but the rule is stricter than v1: a disk is treated as private when it can produce temporary URLs (`providesTemporaryUrls()`) and its config does not explicitly set `'visibility' => 'public'`. Disks that omit the visibility key (common for S3-style drivers) now produce pre-signed URLs.

## Artisan commands

`imgproxy:key` no longer writes to `.env`; it prints the pair as environment lines for you to copy. The `--path` option is gone.

`imgproxy:health` is new: it requests `{url}/health` for the default or a `--instance=` target and exits non-zero on failure.

## What else is new

- Multi-instance manager: `Imgproxy::instance('staging')`.
- Named presets in config, usable from the builder and the components.
- LQIP placeholders: `->placeholder()` on the builder and the `placeholder` component attribute.
- `signature_size` truncation to match the server's `IMGPROXY_SIGNATURE_SIZE`.
- `raw()` escape hatch for imgproxy options not yet covered by a typed method.
- 100% Pest type coverage and golden-vector signing tests, plus live checks against a real imgproxy in Docker (gated behind env vars; see the README).

See [CHANGELOG](CHANGELOG.md) for the full list.
