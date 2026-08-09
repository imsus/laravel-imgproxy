# 03 — Simple HMAC signing

**What to build:** Signed imgproxy URLs. When key and salt are configured (hex strings, decoded to binary), the URL carries a signature segment: URL-safe base64 without padding of HMAC-SHA256(key, salt + path). `signature_size` truncates the signature to match the server's `IMGPROXY_SIGNATURE_SIZE`. With an empty key, URLs are unsigned (local development). Output must match imgproxy's official PHP example byte for byte. A local-only integration test fetches a generated signed URL from a real imgproxy in Docker (env-gated, skipped in CI) to prove signing and encoding against the actual server.

**Blocked by:** 02 — Builder core + encoding

**Status:** ready-for-agent

- [ ] Signed URL with key+salt configured matches the official `examples/signature.php` output byte for byte (golden vector)
- [ ] `signature_size` truncation is honored when configured
- [ ] Empty key produces an unsigned URL (no signature segment)
- [ ] Local-only Docker integration test: generated signed URL fetched from a real imgproxy returns HTTP 200 with an image content type; test skips in CI when the env gate is absent
