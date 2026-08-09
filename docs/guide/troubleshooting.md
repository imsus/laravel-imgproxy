---
title: Troubleshooting
description: Common issues and fixes when working with Laravel imgproxy.
---

# Troubleshooting

## Unsigned URLs Appear Instead of Signed URLs

**Symptom:** Generated URLs contain `unsafe` in the signature slot.

```text
https://imgproxy.example.com/unsafe/rs:fill:300:300/...
```

**Cause:** `IMGPROXY_KEY` or `IMGPROXY_SALT` is missing or empty in your `.env` file.

**Fix:** Set both environment variables with valid hex-encoded values. Use `php artisan imgproxy:key` to generate a fresh pair:

```bash
php artisan imgproxy:key

# Copy the output into your .env file
IMGPROXY_KEY=...
IMGPROXY_SALT=...
```

## Signature Mismatch

**Symptom:** imgproxy returns a signature verification error (e.g. `403 Forbidden` or "expensive security error").

**Cause:** The key, salt, or `signature_size` in your Laravel config does not match what the imgproxy server expects.

**Fix:** Verify that these values match:

1. `IMGPROXY_KEY` and `IMGPROXY_SALT` must be identical on both sides (hex-encoded).
2. If you set `signature_size` in the package config, it must match `IMGPROXY_SIGNATURE_SIZE` on the server.

```env
# Laravel .env
IMGPROXY_KEY=943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881
IMGPROXY_SALT=520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5
```

These must be the same values configured on the imgproxy server as `IMGPROXY_KEY` and `IMGPROXY_SALT`.

## Server-Side Preset Returns 500

**Symptom:** URLs using `preset()` return HTTP 500 from the imgproxy server.

```php
Imgproxy::image($source)->preset('my-preset')->url();
```

**Cause:** The preset is not registered on the imgproxy server. The `preset()` method emits the `pr:` option, which references a server-side preset defined via `IMGPROXY_PRESETS` or `IMGPROXY_PRESETS_PATH`. This is separate from the package's client-side presets defined in `config/laravel-imgproxy.php`.

**Fix:** Register the preset on the imgproxy server, or use the package's [client-side presets](/guide/usage) instead:

```php
// config/laravel-imgproxy.php
'presets' => [
    'my-preset' => [
        'resize' => 'fill',
        'width' => 300,
        'height' => 300,
    ],
],

// Use client-side preset (composes options into the URL, no server config needed)
Imgproxy::image($source)->applyPreset('my-preset')->url();
```

## Base64 vs. Plain Encoding Mismatch

**Symptom:** imgproxy returns errors about an invalid source URL or decoding failure.

**Cause:** The package defaults to URL-safe base64 encoding (`encoding: 'base64'`), but the imgproxy server expects plain encoding (or vice versa).

**Fix:** Ensure the `encoding` setting in your instance config matches the server's expectation:

```php
'instances' => [
    'default' => [
        'encoding' => 'base64', // or 'plain'
    ],
],
```

The package default is `base64`. If your server expects plain-encoded sources (the `plain/` prefix), set `'encoding' => 'plain'`.

## Private Disk Yields Pre-signed URLs Unexpectedly

**Symptom:** A disk that you expect to produce plain `url()` values is generating pre-signed `temporaryUrl()` values instead.

**Cause:** A disk is treated as private when it implements `providesTemporaryUrls()` **and** its config does not explicitly set `'visibility' => 'public'`. S3-style drivers that omit the visibility key fall into this category.

**Fix:** Add an explicit `'visibility' => 'public'` to the disk's config in `config/filesystems.php`:

```php
'disks' => [
    's3' => [
        'driver' => 's3',
        'visibility' => 'public', // Forces url() instead of temporaryUrl()
        // ...
    ],
],
```

See [Storage Integration](/guide/storage-integration) for the full detection logic.

## The Health Command Exits Non-Zero

**Symptom:** `php artisan imgproxy:health` exits with a non-zero status.

**Cause:** The command checks the imgproxy server's `/health` endpoint. A non-zero exit means one of:

- The instance name is not configured.
- The server is unreachable (network/DNS issue).
- The server returned a non-2xx response.

**Fix:** Run with verbose output and check the error message:

```bash
php artisan imgproxy:health

# For a named instance:
php artisan imgproxy:health --instance=staging
```

The command prints a specific error: the instance name, connection failure, or HTTP status code. Verify that `IMGPROXY_URL` is correct and the server is running.
