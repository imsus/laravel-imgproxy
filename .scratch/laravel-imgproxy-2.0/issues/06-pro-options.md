# 06 — Pro options

**What to build:** Every documented pro imgproxy v4 option as a typed fluent method — chained pipelines, object detection, phash/classify, video handling, watermark extras — same pattern and validation as 04/05. Pro options are typed client-side without licensing checks; the server enforces licensing. Tests cover each pro group producing the correct signed URL segment.

**Blocked by:** 04 — Typed core options + raw

**Status:** deferred — reordered last (after 12) pending a pro license for verification; maintainer decision 2026-08-09

- [ ] Every documented pro v4 option has a typed fluent method
- [ ] Invalid values throw `InvalidArgumentException`; valid values produce the correct signed URL segment (tests per concern group)
- [ ] Pro and free options compose together with `raw()` in call order

## Decision (maintainer, 2026-08-09)

No pro license is available, which blocks *verification*, not *implementation*. The package only builds URL strings; imgproxy enforces licensing server-side, so typing pro options needs no license. But the 04/05 verification standard (server source + live probes) is unreachable for pro options:

- The free v4 image does not parse pro options: `ra:lanczos3`, `pg:1`, `aq:ssim:80`, `wmt:YQ`, `car:1:1`, `br:10`, `mc:0.5`, `st:YQ`, `hs:none`, `fiu:YQ` all return 404 "Invalid URL" — identical to a garbage option (`zzz:1`). No parse-level signal to probe.
- The pinned open-source tree (eef3b31) contains no pro parser code: `options/parser/` registers only free options; there are no object-detection, video-thumbnail, autoquality, or chained-pipeline modules.
- `max_bytes` (`mb:`) is **free** in v4 — the free image returned 200 image/png for `mb:100`. The free/pro split must be re-derived from the docs, not assumed.

When 06 is implemented (after 12), the standard is: pin prefixes, argument shapes, defaults, and ranges from the v4 docs (docs.imgproxy.net); compute golden vectors independently (Python); label every pro segment "docs-pinned, not live-verified"; record the gap in this file. Re-verify against a licensed server when one is available.
