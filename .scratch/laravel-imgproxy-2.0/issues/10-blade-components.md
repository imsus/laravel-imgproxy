# 10 — Blade components

**What to build:** Responsive image rendering. `<x-imgproxy-img>` emits an `<img>` with `srcset` built from widths and DPR variants, plus `sizes`, preset support, LQIP placeholder pairing, lazy loading by default, `alt`, and class passthrough. `<x-imgproxy-picture>` emits a `<picture>` with AVIF/WebP `<source>` fallbacks plus srcset. Both accept a source URL or a disk + path. Component views ship in the package and publish as override templates.

**Blocked by:** 08 — Presets + LQIP, 09 — Storage macro + disk

**Status:** ready-for-agent

- [ ] `<x-imgproxy-img>` renders `src`, `srcset` (widths + DPR), `sizes`, preset output, placeholder, `loading="lazy"` default, `alt`, and passed classes
- [ ] `<x-imgproxy-picture>` renders `<source>` elements for AVIF/WebP with srcset plus a fallback `<img>`
- [ ] Both components accept disk + path sources and produce the same URLs as the builder
- [ ] Component views are publishable and overridable; Testbench render tests assert the HTML
