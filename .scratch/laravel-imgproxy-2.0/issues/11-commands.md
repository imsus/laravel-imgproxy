# 11 — Commands

**What to build:** Two artisan commands. `imgproxy:key` generates and prints a key/salt pair for config. `imgproxy:health` GETs `{url}/health` for the default or `--instance=` target, prints status, and exits non-zero on failure. HTTP is mocked in tests.

**Blocked by:** 07 — Manager + facade + helper

**Status:** done

- [x] `imgproxy:key` prints a fresh key/salt pair usable in config
- [x] `imgproxy:health` checks the default instance's `/health` and exits zero on success
- [x] `imgproxy:health --instance=<name>` checks that instance; unknown instance fails
- [x] Non-zero exit and clear output on unreachable instance; tests mock the HTTP client

## Notes

- `imgproxy:key` prints 32-byte hex values as `IMGPROXY_KEY=` and `IMGPROXY_SALT=` lines, matching the `env()` lookups in the published config; it never writes `.env` itself.
- `imgproxy:health` requests `{url}/health` with a 5-second timeout and exits non-zero on unknown instance, non-2xx response, or connection failure. The commands are registered in the provider's `runningInConsole()` guard; `illuminate/http` was added to `require` for the Http client.
- Tests mock HTTP with `Http::fake()` and use `Artisan::call()` + `Artisan::output()`: `PendingCommand::expectsOutputToContain()` matches only one expected substring per output line (Mockery consumes one expectation per `doWrite` call), so same-line multi-substring assertions fail against the one-line status messages.
