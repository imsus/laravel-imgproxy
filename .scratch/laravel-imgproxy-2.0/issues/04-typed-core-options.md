# 04 — Typed core options + raw

**What to build:** The option-assembly mechanism and the first ~15 typed fluent methods: resize, width, height, quality, format, crop, gravity, dpr, blur, sharpen, rotate, enlarge, extend, background, watermark. Typed enums cover resize type, gravity, and output format. Invalid enum values or out-of-range numerics throw `InvalidArgumentException`. A `raw()` method appends option segments verbatim, in call order, for anything untyped. A full fluent chain produces a signed, optioned URL verified by golden vectors.

**Blocked by:** 03 — Simple HMAC signing

**Status:** ready-for-agent

- [ ] ~15 core typed methods exist with enums; invalid values throw `InvalidArgumentException`
- [ ] `raw()` appends verbatim option segments in call order, no validation
- [ ] Chained options appear as ordered signed URL segments; golden-vector tests assert exact composed URLs
- [ ] Builder immutability holds with options applied (base builder reusable for variants)
