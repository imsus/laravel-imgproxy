# 06 — Pro options

**What to build:** Every documented pro imgproxy v4 option as a typed fluent method — chained pipelines, object detection, phash/classify, video handling, watermark extras — same pattern and validation as 04/05. Pro options are typed client-side without licensing checks; the server enforces licensing. Tests cover each pro group producing the correct signed URL segment.

**Blocked by:** 04 — Typed core options + raw

**Status:** ready-for-agent

- [ ] Every documented pro v4 option has a typed fluent method
- [ ] Invalid values throw `InvalidArgumentException`; valid values produce the correct signed URL segment (tests per concern group)
- [ ] Pro and free options compose together with `raw()` in call order
