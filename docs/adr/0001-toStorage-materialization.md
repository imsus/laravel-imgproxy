# Materialize processed images onto Storage disks with `toStorage()`

The builder gained a terminal `toStorage(disk, path, options)` operation that fetches the processed image from imgproxy and writes it to a Laravel Storage disk, returning a `StoredImage` representation, instead of only ever producing imgproxy URLs on demand.

**Why:** some applications want the processed artifact persisted in their own storage — durable copies that survive imgproxy instance changes, key rotation, or the 5-minute expiry of pre-signed private-disk source URLs — and want a plain object URL to serve from their CDN or storage directly.

**Considered and rejected:**

- **URL caching** — regenerate-and-reuse the URL string. Rejected: URL generation is deterministic and costs microseconds; for private-disk sources the stored URL embeds a pre-signed `temporaryUrl()` that expires, so caching the string is a correctness trap.
- **Recipe persistence** — store source + options, regenerate later. Rejected: the user's need is a durable artifact and a stable address, not deferred generation.
- **Returning the raw disk adapter** — rejected in favor of a `StoredImage` value object that carries disk + path and resolves URLs lazily (plain `url()` on public disks, pre-signed `temporaryUrl()` on private ones).

**Consequences:**

- The imgproxy response body is streamed to the disk (`Http` with the `stream` option, `writeStream`); large images never load fully into memory.
- Non-2xx responses throw `ImgproxyStorageException` before any disk write; write failures are wrapped in the same exception with the original error preserved.
- Existing files at the destination path are silently overwritten; a failed mid-stream write can leave a partial object (no atomic rename on object stores) — retries are self-healing.
