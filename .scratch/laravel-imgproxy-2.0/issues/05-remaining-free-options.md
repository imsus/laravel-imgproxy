# 05 — Remaining free options

**What to build:** Every remaining free imgproxy v4 option as a typed fluent method, following the exact pattern from 04 — same validation behavior, same enum discipline, same immutability. Grouped by concern for reviewability. Tests cover each option group producing the correct segment in the signed URL.

**Blocked by:** 04 — Typed core options + raw

**Status:** ready-for-agent

- [ ] Every documented free v4 option has a typed fluent method
- [ ] Invalid values throw `InvalidArgumentException`; valid values produce the correct signed URL segment (tests per concern group)
- [ ] `raw()` and typed methods compose together in call order
