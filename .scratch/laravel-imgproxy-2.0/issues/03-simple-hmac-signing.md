# 03 — Simple HMAC signing

**What to build:** Signed imgproxy URLs. When key and salt are configured (hex strings, decoded to binary), the URL carries a signature segment: URL-safe base64 without padding of HMAC-SHA256(key, salt + path). `signature_size` truncates the signature to match the server's `IMGPROXY_SIGNATURE_SIZE`. With an empty key, URLs are unsigned (local development). Output must match imgproxy's official PHP example byte for byte. A local-only integration test fetches a generated signed URL from a real imgproxy in Docker (env-gated, skipped in CI) to prove signing and encoding against the actual server.

**Blocked by:** 02 — Builder core + encoding

**Status:** done

- [x] Signed URL with key+salt configured matches the official `examples/signature.php` output byte for byte (golden vector)
- [x] `signature_size` truncation is honored when configured
- [x] Empty key produces an unsigned URL with the `unsafe` placeholder signature slot (imgproxy v4 requires the slot; see ticket 02)
- [x] Local-only Docker integration test: generated signed URL fetched from a real imgproxy returns HTTP 200 with an image content type; test skips in CI when the env gate is absent

## Findings (verified against real v4 imgproxy, do not re-derive)

- The signature covers the path **after** the signature slot, leading `/` included: `/{options}/{source}`. The real signature **replaces** the `unsafe` placeholder. Signing the path including `/unsafe` returns 404 from a real v4 server; signing the path without it returns 200.
- **Correction to the earlier docs golden vector:** the signature printed on docs.imgproxy.net for the docs path (`oKfUtW34Dvo2BGQehJFR4Nr0_rIjOtdtzJ3QFsUcXH8`) is **stale** — a real v4 container with key `secret`/salt `hello` rejects it (HTTP 403). The verified signature for the path `/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc` is `Jn6kyi5kKLc44Okrcpq9aNRTu6XHNExAr2L-K7lln1E` (accepted, HTTP 404 only because the source URL is dead). Algorithm confirmed against imgproxy's Go source (`security/signature.go`) and its Go test vectors (`test-key`/`test-salt`/`asd` → `oWaL7QoW5TsgbuiS9-5-DI8S3Ibbo1gdB2SteJh3a20`; truncated to 8 → `oWaL7QoW5Ts`).
- `signature_size` truncates the digest to the first N bytes before base64; a real server with `IMGPROXY_SIGNATURE_SIZE=8` accepts the 8-byte signature and rejects the full one.
- Signing docs: https://docs.imgproxy.net/usage/signing_url — official PHP example in the imgproxy repo's `examples/` folder.

## Local verification config (local-only, not committed or shipped)

- The developer's local imgproxy runs at `http://localhost:8081` with URL signature checking **enabled**.
- `IMGPROXY_KEY` and `IMGPROXY_SALT` are set locally to 64 hex zeros. This is local-only configuration: the integration test reads these values from the environment, never from package code or config.
