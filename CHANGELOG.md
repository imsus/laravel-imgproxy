# Release Notes

## [v2.0.0](https://github.com/imsus/laravel-imgproxy/compare/v1.1.0...v2.0.0) - 2026-08-09

### Breaking changes

- Ground-up rewrite; the v1 API is not compatible. Namespace, facade, config shape, builder methods, and component attributes all changed. See [UPGRADING](UPGRADING.md) for the migration steps.
- Platform: PHP ^8.4 and Laravel 13 only.

### Enhancements

- Immutable fluent URL builder with one typed method per imgproxy v4 processing option, `raw()` escape hatch, and terminal `url()` / `__toString()`.
- Typed enums for resizing type, gravity, output format, and watermark position.
- Simple HMAC signing (hex key + salt) with `signature_size` truncation; unsigned URLs when no key is configured.
- URL-safe base64 source encoding by default with a `plain/` opt-out.
- Multi-instance manager: default plus named instances, each with its own URL, credentials, signature size, and encoding.
- Named presets in config, composable from the builder and the Blade components.
- LQIP placeholder helper (`->placeholder()`), a tiny blurred webp of the same source.
- Responsive Blade components: `<x-imgproxy-img>` (srcset from widths or DPR, sizes, preset, placeholder, lazy loading) and `<x-imgproxy-picture>` (AVIF/WebP sources with a fallback img).
- Storage integration: `Storage::disk(...)->imgproxy($path)` macro and `Builder::disk()`; public disks yield `url()`, private disks a pre-signed `temporaryUrl()`.
- Artisan commands: `imgproxy:key` (prints a fresh key/salt pair) and `imgproxy:health` (checks an instance's `/health` endpoint).
- Global `imgproxy()` helper.
- Golden-vector signing tests and 100% Pest type coverage; live checks against a real imgproxy in Docker, gated behind env vars.
