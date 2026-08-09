# 04 — Typed core options + raw

**What to build:** The option-assembly mechanism and the first ~15 typed fluent methods: resize, width, height, quality, format, crop, gravity, dpr, blur, sharpen, rotate, enlarge, extend, background, watermark. Typed enums cover resize type, gravity, and output format. Invalid enum values or out-of-range numerics throw `InvalidArgumentException`. A `raw()` method appends option segments verbatim, in call order, for anything untyped. A full fluent chain produces a signed, optioned URL verified by golden vectors.

**Blocked by:** 03 — Simple HMAC signing

**Status:** done

- [x] ~15 core typed methods exist with enums; invalid values throw `InvalidArgumentException`
- [x] `raw()` appends verbatim option segments in call order, no validation
- [x] Chained options appear as ordered signed URL segments; golden-vector tests assert exact composed URLs
- [x] Builder immutability holds with options applied (base builder reusable for variants)

## Findings (verified against real v4 imgproxy, do not re-derive)

- Enum values, validation ranges, and segment formats are pinned to the v4 server source (`processing/resize_type.go`, `processing/gravity_type.go`, `imagetype/defs.go`, `options/parser/apply.go`):
  - Resize types: `fit`, `fill`, `fill-down`, `force`, `auto` (no `any` in v4).
  - Gravities: `ce`, `no`, `so`, `ea`, `we`, `nowe`, `noea`, `sowe`, `soea`, `sm`; the `extend` option rejects `sm` (server: `ExtendGravityTypes` excludes smart).
  - Output formats: `jpg`, `png`, `webp`, `avif`, `gif`, `ico`, `svg`, `bmp`, `tiff`, `heic`, `jxl` (the registered v4 output types; `jp2`/`pdf` are not registered in v4 free).
  - Background color: exactly 3 or 6 hex digits (server regex `^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$`); a leading `#` is accepted client-side and stripped.
  - Watermark positions include `re` (repeat, free); `ch` is pro and out of scope.
- Trailing defaults are trimmed: `resize(Fill, 300, 400)` emits `rs:fill:300:400` (enlarge omitted). Verified live: a real v4 imgproxy returns 200 `image/webp` for the signed fluent chain `rs:fill:300:400/g:sm/q:80/f:webp`.
- Golden-vector signatures were computed independently with Python (HMAC-SHA256, base64url, no padding) and pinned in `tests/Unit/BuilderOptionsTest.php`; key `secret`/salt `hello` matches the issue-03 verified pair.
- **Correction (issue 05, 2026-08-09):** the `enlarge` option shortcut was emitted as `en:1`, but v4 (server source `options/parser/processing_options.go` at eef3b31 and a live v4 imgproxy) only registers `enlarge`/`el` — `en:1` returns "Invalid URL" (404). The builder now emits `el:`; the affected golden vectors in `tests/Unit/BuilderOptionsTest.php` were recomputed and re-pinned.
