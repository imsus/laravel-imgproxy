# 10 — Blade components

**What to build:** Responsive image rendering. `<x-imgproxy-img>` emits an `<img>` with `srcset` built from widths and DPR variants, plus `sizes`, preset support, LQIP placeholder pairing, lazy loading by default, `alt`, and class passthrough. `<x-imgproxy-picture>` emits a `<picture>` with AVIF/WebP `<source>` fallbacks plus srcset. Both accept a source URL or a disk + path. Component views ship in the package and publish as override templates.

**Blocked by:** 08 — Presets + LQIP, 09 — Storage macro + disk

**Status:** done

- [x] `<x-imgproxy-img>` renders `src`, `srcset` (widths + DPR), `sizes`, preset output, placeholder, `loading="lazy"` default, `alt`, and passed classes
- [x] `<x-imgproxy-picture>` renders `<source>` elements for AVIF/WebP with srcset plus a fallback `<img>`
- [x] Both components accept disk + path sources and produce the same URLs as the builder
- [x] Component views are publishable and overridable; Testbench render tests assert the HTML

## Notes

- Two class components under `src/View/Components/`: an abstract `ImageComponent` base (shared attributes, constructor validation, srcset building, list normalization) plus `Img` and `Picture`. Registered by the provider as `<x-imgproxy-img>` / `<x-imgproxy-picture>` via `Blade::component`; views live in `resources/views/` under the `imgproxy::` namespace and publish to `resources/views/vendor/imgproxy` with the `laravel-imgproxy-views` tag (also on the combined `laravel-imgproxy` tag).
- Attributes must be constructor-promoted properties: Blade excludes constructor parameter names from the attribute bag, so anything else would re-emit through `{{ $attributes }}` and duplicate attributes. Public zero-arg methods are callable from the views (`$srcUrl()`, `$sources()`, …) via `InvokableComponentVariable` — the same mechanism v1 used with `$buildUrl()`.
- Srcset semantics: `widths` yields `w` descriptors (`url 320w, …`), `dprs` yields `x` descriptors (`url 1x, url 2x`); the two are mutually exclusive because HTML forbids mixing descriptor types in one srcset — combining them throws. Both attributes accept an array (`:widths="[320, 640]"`) or a comma-separated string (`widths="320, 640"`).
- Preset composes first, then width/DPR candidates append — per-URL overrides win, matching issue 08's order-based override semantics (`rs:fill/w:300/h:300/w:640`).
- Placeholder pairing: `placeholder` swaps the `src` to the builder's `->placeholder()` URL (tiny blurred webp, same source + preset) and keeps the full image reachable through the srcset; with no widths/dprs the srcset is a single 1x candidate so the full image still loads. On the picture component the LQIP sits on the fallback `<img>`, which also carries the fallback format's srcset so no-`<picture>` browsers can upgrade.
- Picture formats: every format except the last becomes a `<source>` (`type` = MIME map for all 11 `Format` cases); the last format is the fallback `<img>`. Default `['avif', 'webp', 'jpg']` — AVIF/WebP sources with a JPG fallback, matching the issue's "AVIF/WebP `<source>` fallbacks". `formats` also accepts a comma-separated string.
- Disk + path sources resolve through the shared `DiskUrl` resolver (`imgproxy()->url(DiskUrl::resolve(Storage::disk(...), $path))`), the same code path as the Storage macro and `Builder::disk()`, so the component URLs match the builder byte for byte. Public disks yield `url()`, private disks pre-signed `temporaryUrl()`.
- Blade wraps component-construction exceptions in `ViewException`, so render tests assert `ViewException` with the underlying message; the components throw `InvalidArgumentException` with descriptive messages (missing source, disk without path, src + disk together, widths + dprs together, non-positive width/DPR, unknown preset/format).
- Larastan's `view-string` type calls `view()->exists()` at analysis time and cannot resolve package namespaces, so the `render()` calls carry a targeted `@phpstan-ignore argument.type` (verified: the literal is a valid view at runtime; the ignore is analysis-only).
- Validation: `composer test` green — phpstan level 7 clean, pint clean, pest type coverage 100%, 177 tests passed (2 live-imgproxy skips). Component tests use unsigned golden vectors for readable URLs; disk tests reuse the public-disk config from issue 09's tests.
- The initial "published view override" render test was dropped: it wrote a marker view into the shared Testbench app dir, which raced under `pest --parallel` and left a stale compiled-template landmine. Publishing is asserted by file existence plus a render smoke with the published views in place; override precedence is Laravel's own vendor-namespace mechanism.
