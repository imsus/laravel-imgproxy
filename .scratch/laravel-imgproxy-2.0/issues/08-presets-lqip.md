# 08 — Presets + LQIP

**What to build:** Reusable option sets and blur-up placeholders. Named presets from config compose onto a builder before per-URL overrides (`->applyPreset('thumb')`); presets are shared across instances. `->placeholder()` returns a tiny blurred webp URL (`w:16`, `bl:8`) of the same source, for LQIP/blur-up previews.

**Blocked by:** 04 — Typed core options + withOption

**Status:** done

- [x] A configured preset composes its options onto a builder; per-URL overrides win
- [x] Unknown preset name throws
- [x] `->placeholder()` returns a `w:16` + `bl:8` + webp URL for the same source, correctly signed/encoded
- [x] Presets work identically across instances

## Notes

- The `Builder` now carries the shared preset option sets (constructor 8th param, threaded through `sourceEncoding()` and `withSegment()`). `Manager` passes the top-level `presets` config through `Instance` into the builder, so presets are identical on every instance.
- `->applyPreset($name)` applies the named config entry via a private whitelist (`applyPresetOption`, 31 single-value options) that maps preset keys to typed fluent methods, each value passing an explicit type check (`expectInt`/`expectNumber`/`expectString`/`expectBool`/`expectEnumOrString`) before the typed method validates ranges. Unknown preset names and unknown option keys throw `InvalidArgumentException`. Multi-argument options (crop, trim, padding, watermark, …) are intentionally not preset-able; compose them per-URL or via `raw()`.
- Override-wins is order-based: preset segments append first, chained options after — imgproxy processes options left to right with later values overwriting earlier ones (verified ordering semantics from issue 05).
- `->placeholder()` returns a new builder with `w:16`, `bl:8`, and `f:webp` appended (immutable; the original builder is untouched). It is a builder, not a string — `(string)`/`->url()` yields the placeholder URL, and components (issue 10) pair it with the full-size URL.
- Golden vectors computed independently (Python HMAC-SHA256) and cross-checked byte for byte against the PHP builder; the staging-instance vectors prove the same preset renders with the named instance's encoding (plain) and signature size (8).
- Pro options (`maxSource*`, `preserveHDR`, `enforceThumbnail`, `returnAttachment`, …) are preset-able because their typed methods already exist; consistent with issue 06's deferral — docs-pinned, not live-verified.
