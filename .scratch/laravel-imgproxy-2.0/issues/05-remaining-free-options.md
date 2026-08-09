# 05 — Remaining free options

**What to build:** Every remaining free imgproxy v4 option as a typed fluent method, following the exact pattern from 04 — same validation behavior, same enum discipline, same immutability. Grouped by concern for reviewability. Tests cover each option group producing the correct segment in the signed URL.

**Blocked by:** 04 — Typed core options + raw

**Status:** done

- [x] Every documented free v4 option has a typed fluent method
- [x] Invalid values throw `InvalidArgumentException`; valid values produce the correct signed URL segment (tests per concern group)
- [x] `raw()` and typed methods compose together in call order

## Findings (verified against real v4 imgproxy, do not re-derive)

- All 29 remaining free v4 options are typed on the builder: `size`, `resizingType`, `minWidth`, `minHeight`, `zoom`, `extendAspectRatio`, `focusPoint`, `trim`, `padding`, `autoRotate`, `flip`, `pixelate`, `stripMetadata`, `keepCopyright`, `stripColorProfile`, `preserveHdr`, `enforceThumbnail`, `formatQuality`, `skipProcessing`, `rawResponse`, `cacheBuster`, `expires`, `filename`, `returnAttachment`, `imgproxyPreset`, `maxSrcResolution`, `maxSrcFileSize`, `maxAnimationFrames`, `maxAnimationFrameResolution`, `maxResultDimension`.
- Prefixes and validation ranges are pinned to `options/parser/apply.go`, `options/parser/parse.go`, `options/parser/processing_options.go`, `imagetype/registry.go`, and `vips/color/color.go` at imgproxy master eef3b31, and probed live against the local v4 container (docker.io/imgproxy: v4): every segment parses; the full signed chain returns 200 image/png; `raw:1` streams the source. Security options (`msr`/`msfs`/`maf`/`mafr`/`mrd`) 403 without `IMGPROXY_ALLOW_SECURITY_OPTIONS` — expected server gate, documented on the methods.
- Naming collisions resolved: the imgproxy `raw` processing option is `rawResponse()` (escape hatch `raw()` kept); the server-side `preset` option is `imgproxyPreset()` (config-preset `preset()` is issue 08's).
- Validation boundaries: zoom factors > 0; focus point offsets in 0-1; trim threshold >= 0 (server accepts any float; stricter like 04's blur/sharpen); format quality 0-100 with known formats; security limits non-negative (`maxAnimationFrames` > 0); `expires` non-negative (0 = no expiry; the server rejects already-past timestamps at request time); `cacheBuster`/`filename`/`imgproxyPreset` non-empty.
- The `enlarge` shortcut correction (04's `en:` → `el:`) is recorded in issue 04's findings; issue 05 re-pinned the two affected golden vectors.
