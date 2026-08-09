# 07 — Manager + facade + helper

**What to build:** The Laravel-facing entry point. A multi-instance manager resolves the default instance or a named instance; each instance carries its own URL, key, salt, signature size, and encoding. The `Imgproxy` facade proxies to the manager — `Imgproxy::url(...)` uses the default instance, `Imgproxy::instance('staging')->url(...)` a named one. A global `imgproxy()` helper is the terse alternative. Unknown instance names throw.

**Blocked by:** 03 — Simple HMAC signing

**Status:** done

- [x] `Imgproxy::url($source)` resolves the default instance and produces its signed/encoded URL
- [x] `Imgproxy::instance('staging')->url($source)` uses the named instance's own URL, key, salt, signature size, and encoding
- [x] Unknown instance name throws
- [x] `imgproxy()` helper is equivalent to the facade on the default instance

## Notes

- `Manager::instance()` now returns an `Instance` object (url, key, salt, signature size, encoding) instead of a raw config array; the scaffold-era unit test asserting the array shape was updated. `Manager::url($source, $instance = null)` is the direct builder entry point the facade proxies to.
- An instance with a missing/empty base URL throws `InvalidArgumentException` ("has no URL configured") instead of silently producing a broken relative URL — the default instance's `url` comes from `env('IMGPROXY_URL')`, which is null on a fresh install.
- The facade is deliberately not `final` so applications can mock it (`Imgproxy::shouldReceive(...)`) in their tests.
- Golden vectors in the tests were computed independently (Python HMAC-SHA256) and cross-checked against the PHP `Builder` output byte for byte.
