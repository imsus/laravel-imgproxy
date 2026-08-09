# 07 — Manager + facade + helper

**What to build:** The Laravel-facing entry point. A multi-instance manager resolves the default instance or a named instance; each instance carries its own URL, key, salt, signature size, and encoding. The `Imgproxy` facade proxies to the manager — `Imgproxy::url(...)` uses the default instance, `Imgproxy::instance('staging')->url(...)` a named one. A global `imgproxy()` helper is the terse alternative. Unknown instance names throw.

**Blocked by:** 03 — Simple HMAC signing

**Status:** ready-for-agent

- [ ] `Imgproxy::url($source)` resolves the default instance and produces its signed/encoded URL
- [ ] `Imgproxy::instance('staging')->url($source)` uses the named instance's own URL, key, salt, signature size, and encoding
- [ ] Unknown instance name throws
- [ ] `imgproxy()` helper is equivalent to the facade on the default instance
