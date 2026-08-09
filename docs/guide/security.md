---
title: Security
description: URL signing, credential management, and server-side source protection for imgproxy.
---

# Security

## URL signing

Every imgproxy URL begins with a signature slot. With a key and salt configured, the slot contains an HMAC-SHA256 signature of the exact path; without them, it contains the literal string `unsafe`.

### How it works

1. The builder computes the path emitted — including encoding, option segments, and the source.
2. The HMAC-SHA256 signature covers that exact path, hex-decoded from the key and salt.
3. The signature is truncated to the configured `signature_size` (1–32 bytes); sizes of 32 or more keep the full digest.
4. The result is URL-safe base64 encoded (no padding) and placed in the signature slot.

The signature covers the path **after** the signature slot, leading slash included, exactly as imgproxy verifies it.

### Unsigned URLs

When `IMGPROXY_KEY` or `IMGPROXY_SALT` is absent, URLs are generated unsigned with the `unsafe` slot:

```php
$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/...
```

This is fine for local development. **Never expose unsigned URLs in production** — anyone could craft arbitrary processing requests against your imgproxy server.

### Signed URLs

With both credentials configured, the `unsafe` slot is replaced by the HMAC signature:

```php
// https://imgproxy.example.com/7Fu-sZuoCXRc1LXWhM687mlhsd2SFxXBpiFjJk6vakw/rs:fill:300:300/...
```

### `signature_size`

The `signature_size` config value truncates the signature to match the server's `IMGPROXY_SIGNATURE_SIZE`. Set it to the same value on both sides:

```php
'instances' => [
    'default' => [
        'signature_size' => 16, // must match IMGPROXY_SIGNATURE_SIZE on the server
    ],
],
```

`null` keeps the full 32-byte digest. Sizes of 32 or more behave identically to `null`.

## Generating credentials

The `imgproxy:key` command generates a fresh 32-byte key and salt pair, printed as environment lines:

```bash
php artisan imgproxy:key

Generated a new imgproxy key and salt pair.

IMGPROXY_KEY=...
IMGPROXY_SALT=...
```

Copy the output into your `.env` file. The command does not write to `.env` automatically.

## Keeping credentials safe

- **Never commit** `IMGPROXY_KEY` or `IMGPROXY_SALT` to source control. Use `.env` and environment variables.
- **Rotate keys** periodically — generate a new pair, deploy it, then remove the old one.
- **Use separate keys** for development and production environments.

## Protecting private sources

imgproxy provides server-side limits that prevent abuse even if an attacker has a signed URL. These are configured on the imgproxy server itself (`IMGPROXY_*` environment variables), but the package's builder exposes typed methods for convenience:

```php
Imgproxy::url($source)
    ->maxSrcResolution(16800000)    // maxPixels: 16.8 megapixels
    ->maxSrcFileSize(104857600)     // 100 MB
    ->maxAnimationFrames(200)
    ->maxAnimationFrameResolution(16800000)
    ->maxResultDimension(16800)
    ->url();
```

These methods append server-side option segments to the URL. The imgproxy server enforces them regardless of the client.

## Quick reference

| Concern | What to do |
| --- | --- |
| Signing | Set `IMGPROXY_KEY` and `IMGPROXY_SALT` in `.env` |
| Signature size | Set `signature_size` in config to match `IMGPROXY_SIGNATURE_SIZE` on the server |
| Key generation | `php artisan imgproxy:key` |
| Unsigned URLs | Fine locally; never in production |
| Source protection | Use `maxSrcResolution`, `maxSrcFileSize`, etc. on the builder or the server |
| Credential safety | Never commit to source control; rotate periodically |
