Status: ready-for-agent

# Spec: Intent-method layer for the imgproxy builder

## Problem Statement

The `laravel-imgproxy` builder exposes one typed method per imgproxy v4 processing option, and its headline examples require reaching for the wire vocabulary (`->resize(ResizeType::Fill, 300, 300)`). Laravel 13 shipped a fluent `Image` API, and this package targets Laravel 13 + PHP 8.4 — so a Laravel developer reaching for the package today has to translate familiar intent (`cover`, `fit`, `toWebp`, `storePublicly`) into imgproxy option names and enum args. For the common cases that translate cleanly, the package makes users do more work and read less like Laravel than it should.

## Solution

Add a thin **intent-method** layer to the existing immutable builder, on the 2.x line, with no backward-compatibility break. The intent methods express the desired image outcome in domain terms and compile down to the existing processing-option segments; the typed processing-option layer (and the `withOption()` escape hatch) remains intact as the precise, wire-level escape hatch. The intent methods are the documented headline; the typed layer is the reference.

The design borrows Laravel `Image` method names **only where the imgproxy semantics genuinely align** and deliberately does not expose methods whose semantics diverge (see ADR 0002). Domain vocabulary: *intent method*, *processing option*, *option segment* (see `CONTEXT.md`).

## User Stories

1. As a Laravel developer, I want a `cover()` intent method, so that I can crop-to-cover a source to a target box without reaching for the `ResizeType` enum.
2. As a Laravel developer, I want a `fit()` intent method, so that I can fit a source within a target box (preserving aspect ratio, never upscaling) in a single readable call.
3. As a Laravel developer, I want `cover()` to accept an optional gravity, so that I can anchor the crop without a separate `->gravity()` call composing awkwardly with the resize.
4. As a Laravel developer, I want the intent methods to compose with the rest of the fluent chain, so that `->cover(400, 400)->quality(80)->toWebp()` reads as one coherent pipeline.
5. As a Laravel developer, I want `toWebp()`/`toJpg()`/`toPng()`/`toAvif()` format shortcuts, so that I can pick an output format without the `Format::X` enum.
6. As a Laravel developer, I want an `optimize()` shortcut that defaults to webp + quality 70, so that a default-optimized image is one call, matching Laravel's `optimize()` convention.
7. As a Laravel developer, I want the builder to be `Conditionable`, so that I can apply transformations conditionally with `when()`/`unless()` like other Laravel fluent APIs.
8. As a Laravel developer, I want `storePublicly($disk, $path)` to persist a processed image with public visibility, so that I don't have to remember the `['visibility' => 'public']` options array.
9. As a Laravel developer, I want `Imgproxy::fromStorage($path, $disk)` at the facade entry, so that I can start a build from a Storage disk-path as the *source*, mirroring Laravel's `Image::fromStorage()`.
10. As a Laravel developer, I want `Imgproxy::fromPath($path)` and `Imgproxy::fromUrl($url)`, so that I can express the source kind explicitly at the entry point.
11. As a Laravel developer, I want `orient()`, `flipVertically()`, and `flipHorizontally()`, so that the common orientation and flip operations read as intent rather than `autoRotate()`/`flip(true, false)`.
12. As a Laravel developer reading the docs for the first time, I want the headline examples to use intent methods, so that I learn the idiomatic surface first and only dig into wire options when I need them.
13. As a Laravel developer who already knows imgproxy, I want the typed processing-option methods unchanged, so that I keep precise wire-level control.
14. As a contributor, I want the naming divergences from Laravel (gravity-anchored `crop`, deliberate absence of `contain`/`scale`/coordinate `crop`) to be documented, so that I don't "fix" them later.
15. As a package maintainer, I want `resize()` marked `@deprecated` (docblock-only, runtime unchanged), so that new code is steered toward `fit`/`cover` while existing callers keep working.
16. As a reviewer, I want the intent methods pinned by their published URL output, so that behavior is verifiable and regression-proof.
17. As a package maintainer, I want the change to be purely additive, so that the 2.x line stays safe and no migration is required.
18. As a front-end developer, I want to build a placeholder and a full-size image from one base builder using `when()`, so that placeholder and main stay consistent.
19. As a package maintainer, I want the docs and glossary to use consistent terms (*intent method*, *processing option*, *option segment*), so that the two API layers are unambiguous.
20. As a Laravel developer, I want `cover()` and `fit()` to honor imgproxy's default of not upscaling unless `enlarge` is set, so that behavior matches the server's defaults.

## Implementation Decisions

- **Versioning**: Purely additive on the 2.x line. No method signature changes, no removals, no BC breaks. The `resize` rename idea is deferred to a v3 discussion and is not part of this spec.
- **The intent layer is the headline**: docs and README examples lead with intent methods; the typed processing-option methods remain as the reference/escape-hatch layer (and `withOption()` stays the raw verbatim path).
- **Intent methods added to the builder** (all immutable, return a fresh builder, compile to existing option segments):
  - `fit(int $width, int $height)` → `rs:fit:$width:$height` (covers Laravel's `resize`/`scale` intent, since `fit` never enlarges by default).
  - `cover(int $width, int $height, Gravity|string|null $gravity = null)` → `rs:fill:$width:$height`, plus a `g:` segment when gravity is supplied (the gravity anchors the fill crop).
  - `orient()` → alias of `autoRotate()`.
  - `flipVertically()` → `fl:0:1`; `flipHorizontally()` → `fl:1:0`.
  - `toWebp()`/`toJpg()`/`toPng()`/`toAvif()` → `f:webp`/`f:jpg`/`f:png`/`f:avif`.
  - `optimize(Format|string|null $format = null, int $quality = 70)` → `f:<format>` + `q:<quality>` (defaults webp, q70).
  - `storePublicly(string $disk, string $path, array $options = [])` → delegates to `toStorage(..., ['visibility' => 'public'])`.
- **Not added** (deliberate, per ADR 0002): `contain()` (needs a `fit` + `extend_aspect_ratio` + `bg` compound, not a single option), `scale()` (redundant with `fit`), coordinate `crop()` (imgproxy `crop` is gravity-anchored only), `grayscale()` (no free imgproxy option). The `contain` composition is documented in `advanced-usage` instead.
- **`Conditionable`**: the builder uses Laravel's `Conditionable` trait, giving `when()`/`unless()` for free.
- **Facade/`Manager` entry points**: add `fromStorage(string $path, string $disk, ?string $instance = null)` (resolves the disk source exactly like the Storage macro: public → `url()`, private → `temporaryUrl()`), and `fromPath(string $path, ?string $instance = null)` / `fromUrl(string $url, ?string $instance = null)` (both delegate to `image()`). `fromStorage` takes `(path, disk)` — source-subject-first, mirroring Laravel's `Image::fromStorage(path, disk)`. The existing `image()` stays as the raw-source entry.
- **`storePubliclyAs` is not added**: `toStorage($disk, $path)` already takes the full destination path, so there is no auto-filename step for `storePubliclyAs` to bypass — it would be byte-identical to `storePublicly`.
- **`resize` deprecation**: `resize(...)` gets a `@deprecated` docblock (static-analysis/IDE only; no runtime change) pointing to `fit`/`cover` for the common `Fill`/`Fit` cases. No other processing-option method is deprecated in this change.
- **Registry/dependency surface**: no new classes; `fromStorage`/`fromPath`/`fromUrl` live on the existing `Manager` and facade; the new builder methods live on the existing `Builder`. No config or service-provider shape changes.

## Testing Decisions

- **What makes a good test**: Assert external behavior only — the exact URL string produced, the state written to a Storage disk, and the returned builder's behavior. Do not assert internals (builder state, manager internals). Golden-string assertions on `url()`/`__toString()` are the strongest signal.
- **Seam 1 — URL builder (pure PHP, highest seam; unchanged)**: Assert the exact signed/unsigned URL path for each intent method (`fit`, `cover`, `orient`, `flipVertically`/`flipHorizontally`, `toWebp`/`toJpg`/`toPng`/`toAvif`, `optimize`) and for chained `when()`/`unless()`. Also a regression guard that `resize(...)->url()` still emits the correct `rs:` segment (deprecation is docblock-only, so no runtime behavior change is asserted beyond URL output). No Laravel container required. Prior art: `tests/Unit/BuilderTest.php`, `BuilderOptionsTest.php`, `BuilderSigningTest.php`.
- **Seam 2 — Laravel surface (Testbench; unchanged)**: `storePublicly` (Storage fake, assert the file is written with `visibility => public`); `fromStorage` (assert it resolves identically to `Storage::disk($disk)->imgproxy($path)`, covering public → `url()` and private → `temporaryUrl()` with fake disks); `fromPath`/`fromUrl` (assert they delegate to `image()` and return a builder). Prior art: `tests/Feature/ImgproxyToStorageTest.php`, `ImgproxyFacadeTest.php`.
- **Why two seams**: `storePublicly` and `fromStorage` are inherently Storage/Laravel-container concerns (disk resolution, visibility, fake disks), so they cannot collapse into Seam 1 without introducing a lower seam. Two is the natural minimum for the existing architecture.

## Out of Scope

- `contain()`, `scale()`, coordinate `crop()`, `grayscale()` intent methods — deliberately not added (see ADR 0002); the `contain` composition is documented, not exposed.
- The `resize` rename or any `@deprecated` beyond `resize` — deferred to a v3 discussion.
- Any BC break, method removal, or migration shim. This is additive.
- Any method that requires fetching processed bytes (`toBytes`, `toBase64`, `toDataUri`, `mimeType`, `width`, `height`, `dimensions`, `dominantColor`) — those need an HTTP round-trip and belong to the `toStorage`/materializing path, not the URL builder.
- New test seams or architectural changes; the existing two-seam structure is used as-is.
- Config, service-provider, or command changes.

## Further Notes

- **Docs**: README and `docs/guide` examples should lead with intent methods; `advanced-usage` documents the `contain` composition (`fit` + `extend_aspect_ratio` + `background`). The glossary in `CONTEXT.md` already captures *intent method*, *processing option*, *option segment*; ADR 0002 records the borrow-where-aligns naming policy and the explicit no's.
- **`orient`/`flipVertically`/`flipHorizontally`** are readability aliases over existing behavior; they add no new option segment.
- Because the change is additive and docblock-only for the deprecation, existing tests should continue to pass without modification; new tests cover only the added behaviors.
