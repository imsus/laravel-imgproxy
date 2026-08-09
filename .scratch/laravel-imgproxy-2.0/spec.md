Status: ready-for-agent

# Spec: laravel-imgproxy 2.0.0

## Problem Statement

The Laravel developer wants to serve processed images from an imgproxy server: a source image URL, a set of processing options, and a signed URL that imgproxy will honor. Existing PHP packages are fragmented — thin core builders (onliner, crocodile2u) without Laravel integration, or wrappers (hosmelq, rikodev) that predate imgproxy v4 and offer no responsive-image support. The published `imsus/laravel-imgproxy` v1.1.0 covers part of the surface but is built on a scaffold the maintainer no longer wants (spatie package-tools, v3-era options, no srcset, no multi-instance).

The developer needs one maintained Laravel package that: covers the full imgproxy v4 option surface (free + pro), signs URLs correctly, renders responsive `<picture>`/`<img>` output with srcset and LQIP placeholders, builds source URLs from Laravel Storage disks (pre-signed when private), supports multiple imgproxy instances, and is verified against a real imgproxy server locally.

## Solution

Release `imsus/laravel-imgproxy` 2.0.0, a ground-up rewrite (fresh scaffold, no v1 API compatibility). The package exposes:

- An immutable fluent URL builder (`Imgproxy::url($source)`) with one typed method per imgproxy v4 option, a `raw()` escape hatch for future options, and terminal `url()` / `__toString()`.
- Simple HMAC signing (key + salt, hex-encoded in config), URL-safe base64 output without padding, `signature_size` truncation support, and unsigned URLs when no key is configured.
- URL-safe base64 source encoding by default with a `plain/` opt-out.
- A multi-instance manager: a default instance plus named instances, each with its own URL, key, salt, signature size, and encoding.
- Named presets in config — reusable option sets shared across instances, usable from the builder and from Blade components.
- Two Blade components: `<x-imgproxy-img>` (srcset from widths + DPR, preset, LQIP placeholder, lazy loading, alt, class passthrough) and `<x-imgproxy-picture>` (AVIF/WebP `<source>` fallbacks plus srcset).
- A Storage macro: `Storage::disk('s3')->imgproxy($path)` — public disks yield `url()`, private disks yield `temporaryUrl()`; equivalent `->disk($disk, $path)` on the builder.
- Two artisan commands: `imgproxy:key` (generates a key/salt pair) and `imgproxy:health` (checks an instance's `/health` endpoint).
- An LQIP placeholder helper (`->placeholder()` on the builder, a component attribute) defaulting to a tiny blurred webp (`w:16`, `bl:8`).

Target platform: PHP ^8.4, Laravel 13 only.

## User Stories

1. As a Laravel developer, I want to generate a processed image URL from a source URL with the `Imgproxy` facade, so that I can serve transformed images.
2. As a Laravel developer, I want the builder to chain processing options as typed methods (resize, width, height, quality, format, crop, gravity, dpr, blur, sharpen, rotate, enlarge, extend, background, watermark, and all other free + pro v4 options), so that I get autocomplete and type safety.
3. As a Laravel developer, I want a `raw()` method that appends option segments verbatim, so that new or niche imgproxy options work without waiting for a package release.
4. As a Laravel developer, I want an immutable builder, so that I can safely reuse a base builder for several URL variants without accidental mutation.
5. As a Laravel developer, I want signed URLs when key and salt are configured, so that my imgproxy endpoint is protected from unauthorized use.
6. As a Laravel developer, I want unsigned URLs when no key is configured, so that local development works with zero setup.
7. As a Laravel developer, I want `signature_size` truncation, so that generated signatures match the server's `IMGPROXY_SIGNATURE_SIZE` setting.
8. As a Laravel developer, I want URL-safe base64 source encoding by default, so that URLs are compact and free of escaping bugs.
9. As a Laravel developer, I want a `plain/` encoding opt-out, so that I can debug and read generated URLs.
10. As a Laravel developer, I want named presets defined in config, so that I can reuse option sets across the application and keep markup terse.
11. As a Laravel developer, I want a multi-instance manager, so that I can point different environments (staging, production, tenants) at different imgproxy servers with different keys.
12. As a Laravel developer, I want to build a source URL from a Storage disk and path, so that images on S3 or local disks can be processed without hand-building URLs.
13. As a Laravel developer, I want the Storage macro to detect public vs private disks, so that private images automatically get pre-signed temporary URLs.
14. As a front-end developer, I want `<x-imgproxy-img>` to emit an `img` with a srcset built from widths and DPR variants, so that browsers download the right size.
15. As a front-end developer, I want `<x-imgproxy-picture>` to emit AVIF/WebP `<source>` fallbacks plus srcset, so that modern formats are used where supported.
16. As a front-end developer, I want a placeholder attribute for LQIP/blur-up previews, so that pages paint fast before the full image loads.
17. As a front-end developer, I want lazy loading, alt text, and class passthrough on components, so that rendered images are accessible and performant.
18. As a Laravel developer, I want presets usable inside components, so that responsive image sets stay consistent across the app.
19. As a Laravel developer, I want a global `imgproxy()` helper, so that I have a terse alternative to the facade.
20. As a package maintainer, I want a sensible default instance from env vars (`IMGPROXY_URL`, `IMGPROXY_KEY`, `IMGPROXY_SALT`), so that the package works after minimal configuration.
21. As a package maintainer, I want the `imgproxy:key` command, so that I can generate and print a key/salt pair for config.
22. As an operations engineer, I want the `imgproxy:health` command (with `--instance`), so that I can verify an instance is reachable and get a non-zero exit on failure.
23. As a Laravel developer, I want invalid option values to throw, so that mistakes surface at build time instead of as broken images.
24. As a package maintainer, I want the package to be verifiable against a real imgproxy server locally, so that signing and encoding are proven against the actual server.
25. As a reviewer, I want 100% type coverage and golden-vector signing tests, so that the package is statically safe and URL output is pinned to imgproxy's official examples.
26. As a package maintainer, I want the 2.0.0 release to supersede v1.1.0 on Packagist with a documented 1.x → 2.0.0 upgrade note, so that existing users can migrate knowingly.

## Implementation Decisions

- **Package identity**: Composer name stays `imsus/laravel-imgproxy`. 2.0.0 supersedes v1.1.0 on Packagist. Namespace `LaravelImgproxy\LaravelImgproxy`. Facade class `Imgproxy`; global helper `imgproxy()`. No v1 API compatibility shims; the upgrade note documents the changes. The scaffold's placeholder command, placeholder config value, and empty main class are removed.
- **URL builder**: One immutable fluent builder class. One typed method per documented imgproxy v4 option (free and pro), grouped by concern. Typed enums for resize type, gravity, and output format. `raw(string)` appends v4 option segments verbatim, in order. Terminal `url()` and `__toString()` return the full URL. Every mutating method returns a new instance.
- **Signing**: Config `key` and `salt` are hex strings, `hex2bin`-decoded. Signature = URL-safe base64 without padding of `HMAC-SHA256(key, salt + path)`. `signature_size` (nullable) truncates to the first N bytes to match `IMGPROXY_SIGNATURE_SIZE`. Empty key ⇒ unsigned URL (no signature segment). Signing must match imgproxy's official PHP example (`examples/signature.php`) byte for byte.
- **Source encoding**: Default URL-safe base64 without padding. `plain/` form percent-encodes the source. The signature covers the exact path emitted, so encoding choice and signature must be computed together. Encoding is configurable per instance with a per-URL override.
- **Multi-instance manager**: A manager resolves the default instance or a named instance (`Imgproxy::instance('staging')`). Each instance carries `url`, `key`, `salt`, `signature_size`, `encoding`. The facade proxies to the manager, defaulting to the default instance.
- **Presets**: Top-level config section; named option sets shared across all instances. A preset composes onto a builder before per-URL overrides. Usable from the builder (`->preset('thumb')`) and from components (`preset="thumb"`).
- **Config shape**: `default` (instance name), `instances` (map of name → `url`, `key`, `salt`, `signature_size`, `encoding`), `presets` (named option sets). Default instance reads `IMGPROXY_URL`, `IMGPROXY_KEY`, `IMGPROXY_SALT` env vars.
- **Storage integration**: Macro on the Storage facade: `Storage::disk('s3')->imgproxy($path)`. Public disks (URL-visible) yield `url()`; private disks yield `temporaryUrl()`. Equivalent `->disk($disk, $path)` method on the builder. Components accept `disk` + `path` attributes.
- **Blade components**: Two class-based components registered by the service provider. `<x-imgproxy-img>`: source (URL or disk+path), `widths` (srcset widths), `sizes`, `preset`, `placeholder` (LQIP), `loading` (lazy default), `alt`, class passthrough. `<x-imgproxy-picture>`: formats (AVIF/WebP fallback), widths, `sizes`, `preset`, `placeholder`, alt, class passthrough. Component views ship in the package and are published as override templates.
- **LQIP placeholder**: `->placeholder()` on the builder returns a URL for a tiny blurred webp (`w:16`, `bl:8`) of the same source. Component `placeholder` attribute pairs it with the full-size URL.
- **Commands**: `imgproxy:key` generates and prints a key/salt pair. `imgproxy:health` GETs `{url}/health` for the default or `--instance=` target, printing status and exiting non-zero on failure.
- **Service provider**: merges config, registers the manager singleton, the facade, the Storage macro, the components, and the commands; publishes config and views.
- **Platform**: PHP ^8.4, Laravel 13 only (adjust the scaffold's ^8.3 / 12||13). Pest, 100% type coverage enforced, Larastan clean.
- **Error behavior**: typed methods validate their arguments; invalid enum values or out-of-range numeric values throw `InvalidArgumentException`. `raw()` is a verbatim passthrough without validation.

## Testing Decisions

- **What makes a good test**: Assert external behavior only — the exact URL string produced, the rendered HTML of components, the return value of the Storage macro, command output and exit codes. Do not assert internals (builder state, manager internals). Exact-string assertions against golden vectors are the strongest signal.
- **Seam 1 — URL builder (pure PHP, highest seam)**: Tests assert the full URL string for a given source + options, covering option assembly, base64 and plain encoding, signing, `signature_size` truncation, presets, and instance selection. Golden vectors come from imgproxy's official `examples/signature.php` and documented URL examples — byte-for-byte expected strings. No Laravel container required.
- **Seam 2 — Laravel surface (Testbench)**: Component rendering (render `<x-imgproxy-img>` and `<x-imgproxy-picture>`, assert `src`, `srcset`, `<source>` elements, placeholder, lazy, alt, classes), the Storage macro (public disk → `url()`, private disk → `temporaryUrl()` using fake disks), and the two artisan commands (assert output and exit codes; mock the HTTP client for `imgproxy:health`).
- **Live check (local only, not a seam)**: An integration test that generates a signed URL and fetches it from a real imgproxy running in Docker, asserting HTTP 200 and an image content type. Gated behind an env var set only in local development (the user's existing Docker imgproxy); skipped in CI — CI runs golden vectors + mocked tests only, adding no CI cost.
- **Prior art**: The scaffold's Pest setup, `ArchTest`, and `composer test` pipeline; type coverage enforced at 100% via the existing `test:types` script. Signing tests mirror the imgproxy project's own official PHP example.

## Out of Scope

- Advanced signing (AES-CBC encrypted source URLs). Simple HMAC only; advanced can be added later without breaking the builder.
- Upload APIs — imgproxy has no upload endpoint; this package only generates URLs.
- Server-side source schemes (`local://`, `s3://` direct-to-imgproxy) — these are imgproxy server configuration concerns; arbitrary scheme strings pass through encoding unchanged.
- Laravel < 13 and PHP < 8.4 support.
- v1 API compatibility, aliases, or migration shims — the 2.0.0 upgrade note documents the break instead.
- A multi-context domain layout, `CONTEXT.md`, or ADRs — not needed for this build; the spec is the primary source.

## Further Notes

- v1.1.0 remains on Packagist until the 2.0.0 release lands.
- Scaffold items to reconcile: `composer.json` platform bump (PHP ^8.4, illuminate ^13), remove the placeholder command/config/class, decide view-publish contents against the new components.
- The integration test depends on the user's local Docker imgproxy; its URL must be documented in the test file so CI skip behavior is obvious.
- Release process: README rewrite, CHANGELOG entry, 2.0.0 tag, then `composer test` full validation before publishing (per repo AGENTS.md).
