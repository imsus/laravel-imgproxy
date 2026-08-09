# 09 — Storage macro + disk

**What to build:** Sources from Laravel Storage disks. A macro on the Storage facade — `Storage::disk('s3')->imgproxy($path)` — yields the public `url()` for public disks and a pre-signed `temporaryUrl()` for private disks. The builder gets an equivalent `->disk($disk, $path)` method.

**Blocked by:** 07 — Manager + facade + helper

**Status:** ready-for-agent

- [ ] `Storage::disk(<public>) ->imgproxy($path)` produces a URL from the disk's `url()`
- [ ] `Storage::disk(<private>) ->imgproxy($path)` produces a pre-signed `temporaryUrl()` source
- [ ] Builder `->disk($disk, $path)` matches the macro behavior on both disk types
- [ ] Tests use fake disks; no real cloud credentials
