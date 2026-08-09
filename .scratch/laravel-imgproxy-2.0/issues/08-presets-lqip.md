# 08 — Presets + LQIP

**What to build:** Reusable option sets and blur-up placeholders. Named presets from config compose onto a builder before per-URL overrides (`->preset('thumb')`); presets are shared across instances. `->placeholder()` returns a tiny blurred webp URL (`w:16`, `bl:8`) of the same source, for LQIP/blur-up previews.

**Blocked by:** 04 — Typed core options + raw

**Status:** ready-for-agent

- [ ] A configured preset composes its options onto a builder; per-URL overrides win
- [ ] Unknown preset name throws
- [ ] `->placeholder()` returns a `w:16` + `bl:8` + webp URL for the same source, correctly signed/encoded
- [ ] Presets work identically across instances
