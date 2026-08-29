# Layer a Laravel-idiomatic intent-method API over imgproxy's processing options

The builder keeps its exhaustive "processing option" layer (every imgproxy v4 option exposes one typed method) and gains a thin "intent method" layer — `cover`, `fit`, `toWebp`/`optimize`, `storePublicly`, and Laravel's `Conditionable` `when()`/`unless()` — that expresses image outcomes in domain terms rather than imgproxy wire tokens. Names are borrowed from Laravel 13's `Image` API **only where the imgproxy semantics genuinely align**.

**Why:** Laravel 13 shipped a fluent `Image` API and this package targets Laravel 13 + PHP 8.4, so it should feel idiomatic next to it. But imgproxy is a remote processing server, so parts of the Laravel surface don't map one-to-one; the package keeps its low-level typed layer as a deliberate escape hatch for users who know the wire format.

**Considered and rejected:**

- **Mirror every Laravel method name** (`contain`, `scale`, coordinate `crop`) — rejected. `contain` needs a *compound* (`fit` + `extend_aspect_ratio` + `bg`), not a single option; `scale` is redundant with imgproxy's default `fit` (which already never enlarges); imgproxy's `crop` is **gravity-anchored only** (no x/y pixel coordinates), so a coordinate-shaped `crop()` would mislead a Laravel reader.
- **Add `contain`/`scale` anyway for parity** — rejected. The composition (`fit` + `extend_aspect_ratio` + `background`) is documented in `advanced-usage` instead of hidden behind a name that can't faithfully honor it.
- **Rename/deprecate the whole typed option layer** — rejected as a v2.x change. The design is purely additive; the `resize` rename is deferred to a v3 discussion and only `resize` carries `@deprecated` (superseded by `cover`/`fit`).

**Consequences:**

- Two-layer public API: **intent methods** as the documented headline; **processing option** methods as the precise, escape-hatch layer (plus raw `withOption()`).
- `orient` is added as an alias of `autoRotate`; `flipVertically`/`flipHorizontally` for readability over `flip(true, false)`.
- Docs lead with intent methods; `advanced-usage` documents the `contain` composition.
- New glossary terms: *intent method*, *processing option*, *option segment*.
