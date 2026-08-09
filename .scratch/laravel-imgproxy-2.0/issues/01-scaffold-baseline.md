# 01 — Scaffold baseline

**What to build:** A clean, validated package skeleton that later tickets build on: the platform and structure prefactor. The package targets PHP ^8.4 and Laravel 13 only. The scaffold's placeholder command, placeholder config value, and empty main class are gone. The real config surface exists — a default instance name, an instances map, and a presets section — and the service provider merges it and registers the manager as a singleton.

**Blocked by:** None — can start immediately

**Status:** done

- [x] Composer metadata targets PHP ^8.4 and Laravel 13 only; `composer test`, `composer lint:check`, and `composer analyse` all pass on the skeleton
- [x] Placeholder command, placeholder config value, and empty main class are removed
- [x] Published config contains: default instance name, `instances` map (each with `url`, `key`, `salt`, `signature_size`, `encoding`), and `presets` section; default instance reads `IMGPROXY_URL`, `IMGPROXY_KEY`, `IMGPROXY_SALT` env vars
- [x] Service provider merges the config and registers the manager as a singleton
