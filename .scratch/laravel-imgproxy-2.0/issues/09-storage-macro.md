# 09 — Storage macro + disk

**What to build:** Sources from Laravel Storage disks. A macro on the Storage facade — `Storage::disk('s3')->imgproxy($path)` — yields the public `url()` for public disks and a pre-signed `temporaryUrl()` for private disks. The builder gets an equivalent `->disk($disk, $path)` method.

**Blocked by:** 07 — Manager + facade + helper

**Status:** done

- [x] `Storage::disk(<public>) ->imgproxy($path)` produces a URL from the disk's `url()`
- [x] `Storage::disk(<private>) ->imgproxy($path)` produces a pre-signed `temporaryUrl()` source
- [x] Builder `->disk($disk, $path)` matches the macro behavior on both disk types
- [x] Tests use fake disks; no real cloud credentials

## Notes

- Macro is registered on `Illuminate\Filesystem\FilesystemAdapter` (adapter-level, so `Storage::disk(...)->imgproxy(...)` works) in the provider's `boot()`; the bound `$this` is the disk adapter. Returns a `Builder`, consistent with the package currency.
- `src/DiskUrl` is the shared resolver used by both the macro and `Builder::disk()`. Public/private rule: `providesTemporaryUrls()` (Laravel's own capability flag) AND config `visibility !== 'public'` → pre-signed `temporaryUrl()`; otherwise the plain `url()`. A public disk that *can* produce temporary URLs still yields `url()` — visibility wins.
- `->disk($disk, $path, $expiration)` accepts `int|DateTimeInterface|null` — int is seconds from now, `DateTimeInterface` an absolute time; default 5 minutes. This Laravel version's `FilesystemAdapter::temporaryUrl()` is typed `DateTimeInterface`, so ints convert via `now()->addSeconds()`.
- This Laravel 13 has no `Storage::fake()`/`FilesystemFake`; tests fake disks the native way: Testbench's `public` disk (explicit `visibility => public`, `url` overridden to `https://cdn.example.com` for deterministic vectors) and a local disk with `buildTemporaryUrlsUsing()` for the private case. No cloud credentials anywhere.
- Clock is frozen (`Carbon::setTestNow`) in the private-disk tests so the presigned-URL golden vectors are deterministic; reset in `afterEach`.
- `LaravelImgproxyServiceProvider` stays non-final (pre-existing) — service providers are Laravel's extension point, like the non-final facade.
