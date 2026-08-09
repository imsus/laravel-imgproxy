# 02 — Builder core + encoding

**What to build:** The immutable fluent builder at its thinnest complete path: given a source image URL, it produces a correct imgproxy URL string. Source URLs are URL-safe base64-encoded without padding by default, with a `plain/` opt-out that percent-encodes. Processing options assemble as an ordered segment list between the signature slot and the source. No signing yet — URLs are unsigned. Golden-vector tests pin exact URL strings against imgproxy's documented examples.

**Blocked by:** 01 — Scaffold baseline

**Status:** done

- [x] `url($source)` returns the imgproxy base URL + base64-encoded source (URL-safe alphabet, no padding); `plain/` variant percent-encodes the source
- [x] Builder is immutable: every mutating call returns a new instance; the original is unchanged
- [x] Option segments assemble in call order between signature slot and source (unsigned URLs for now)
- [x] Golden-vector tests assert exact URL strings from imgproxy's documented examples
