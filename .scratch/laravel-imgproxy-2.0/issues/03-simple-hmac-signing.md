# 03 — Simple HMAC signing

**What to build:** Signed imgproxy URLs. When key and salt are configured (hex strings, decoded to binary), the URL carries a signature segment: URL-safe base64 without padding of HMAC-SHA256(key, salt + path). `signature_size` truncates the signature to match the server's `IMGPROXY_SIGNATURE_SIZE`. With an empty key, URLs are unsigned (local development). Output must match imgproxy's official PHP example byte for byte. A local-only integration test fetches a generated signed URL from a real imgproxy in Docker (env-gated, skipped in CI) to prove signing and encoding against the actual server.

**Blocked by:** 02 — Builder core + encoding

**Status:** ready-for-agent

- [ ] Signed URL with key+salt configured matches the official `examples/signature.php` output byte for byte (golden vector)
- [ ] `signature_size` truncation is honored when configured
- [ ] Empty key produces an unsigned URL with the `unsafe` placeholder signature slot (imgproxy v4 requires the slot; see ticket 02)
- [ ] Local-only Docker integration test: generated signed URL fetched from a real imgproxy returns HTTP 200 with an image content type; test skips in CI when the env gate is absent

## Findings from ticket 02 (verified, do not re-derive)

- The signature covers the path **after** the signature slot, leading `/` included: `/{options}/{source}`. The real signature **replaces** the `unsafe` placeholder. Signing the path including `/unsafe` returns 404 from a real v4 server; signing the path without it returns 200.
- Verified docs golden vector: key `secret` (hex `736563726574`), salt `hello` (hex `68656C6C6F`), path `/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1w/bGUuY29tL2ltYWdl/cy9jdXJpb3NpdHku/anBn.png` → signature `oKfUtW34Dvo2BGQehJFR4Nr0_rIjOtdtzJ3QFsUcXH8`. URL-safe base64 of the HMAC-SHA256 digest, no padding.
- Signing docs: https://docs.imgproxy.net/usage/signing_url — official PHP example in the imgproxy repo's `examples/` folder.

## Local verification config (local-only, not committed or shipped)

- The developer's local imgproxy runs at `http://localhost:8081` with URL signature checking **enabled**.
- `IMGPROXY_KEY` and `IMGPROXY_SALT` are set locally to 64 hex zeros. This is local-only configuration: the integration test reads these values from the environment, never from package code or config.
