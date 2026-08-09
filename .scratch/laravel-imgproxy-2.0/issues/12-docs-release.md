# 12 — Docs + release

**What to build:** The releasable package. README rewritten for the 2.0.0 API (install, config, builder, options, presets, components, storage, commands, LQIP, the local Docker integration test). A 1.x → 2.0.0 upgrade note documents the breaking changes. CHANGELOG entry. Full validation (`composer test`, `composer lint:check`, `composer analyse`) green, then the 2.0.0 tag. v1.1.0 stays on Packagist until this release lands.

**Blocked by:** 10 — Blade components, 11 — Commands (all functional tickets)

**Status:** done

- [x] README documents install, config, builder, options, presets, components, storage, commands, and LQIP with runnable examples
- [x] Upgrade note 1.x → 2.0.0 lists the breaking changes and migration steps
- [x] CHANGELOG entry for 2.0.0
- [x] `composer test`, `composer lint:check`, `composer analyse` all green on the finished package
- [x] 2.0.0 tagged; v1.1.0 remains available until the release is published

## Notes

- README rewritten for the 2.0.0 API: install, env/config, builder + options (grouped method reference, enums, `raw()`), signing, immutability, presets, LQIP, components, Storage, commands, and the Docker-gated live integration test. Every example URL was generated from the real package so the outputs are byte-accurate.
- `UPGRADING.md` covers the 1.x → 2.0.0 breaks: namespace, helper/facade signatures, builder methods + `build()` → `url()`, URL shape (v4 short segments, base64 source), config file move + key renames, component attributes, Storage macro terminal, and the `imgproxy:key` behavior change (no longer writes `.env`; v1's `--path` option is gone).
- CHANGELOG v2.0.0 entry lists the rewrite under Breaking changes / Enhancements.
- Fixed two tests that were not hermetic against exported `IMGPROXY_*` env vars (the documented live-test workflow): `ServiceProviderTest`'s env test now overrides and restores all three sources `env()` reads (`$_ENV`, `$_SERVER`, `getenv`), and the published-views render test pins key/salt to null instead of relying on the environment. With the live vars exported, all 186 tests pass including the two real-imgproxy checks; without them, 184 pass with the same 2 live skips.
- Aligned the CI matrix in `.github/workflows/tests.yml` with the platform (PHP 8.4/8.5 × Laravel 13, testbench 11.*); the previous 8.3/L12 entries could not install against composer.json's constraints.

## Follow-up

- Added a workbench playground for human review (`composer build && composer serve` → http://127.0.0.1:8000): live URL builder demos, presets, Blade components, storage, and command reference, all rendered through the real package and a local imgproxy. `testbench.yaml` now registers the package provider and discovers workbench views; `workbench/config/laravel-imgproxy.php` holds the demo presets, merged by `WorkbenchServiceProvider` outside the test suite.
- The Presets section distinguishes client-side presets (`preset()`, composed into the URL, no server config) from imgproxy's server-side presets (`imgproxyPreset()` → `pr:`, must be registered on the server via `IMGPROXY_PRESETS` or the server answers 404/500). The playground demonstrates the failure mode against the local Docker imgproxy (no presets registered → HTTP 404). README carries the same distinction.
