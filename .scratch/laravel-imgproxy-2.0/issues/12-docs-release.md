# 12 — Docs + release

**What to build:** The releasable package. README rewritten for the 2.0.0 API (install, config, builder, options, presets, components, storage, commands, LQIP, the local Docker integration test). A 1.x → 2.0.0 upgrade note documents the breaking changes. CHANGELOG entry. Full validation (`composer test`, `composer lint:check`, `composer analyse`) green, then the 2.0.0 tag. v1.1.0 stays on Packagist until this release lands.

**Blocked by:** 10 — Blade components, 11 — Commands (all functional tickets)

**Status:** ready-for-agent

- [ ] README documents install, config, builder, options, presets, components, storage, commands, and LQIP with runnable examples
- [ ] Upgrade note 1.x → 2.0.0 lists the breaking changes and migration steps
- [ ] CHANGELOG entry for 2.0.0
- [ ] `composer test`, `composer lint:check`, `composer analyse` all green on the finished package
- [ ] 2.0.0 tagged; v1.1.0 remains available until the release is published
