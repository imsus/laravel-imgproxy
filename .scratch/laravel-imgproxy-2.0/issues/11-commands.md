# 11 — Commands

**What to build:** Two artisan commands. `imgproxy:key` generates and prints a key/salt pair for config. `imgproxy:health` GETs `{url}/health` for the default or `--instance=` target, prints status, and exits non-zero on failure. HTTP is mocked in tests.

**Blocked by:** 07 — Manager + facade + helper

**Status:** ready-for-agent

- [ ] `imgproxy:key` prints a fresh key/salt pair usable in config
- [ ] `imgproxy:health` checks the default instance's `/health` and exits zero on success
- [ ] `imgproxy:health --instance=<name>` checks that instance; unknown instance fails
- [ ] Non-zero exit and clear output on unreachable instance; tests mock the HTTP client
