---
title: Installation
description: Install Laravel imgproxy, publish config, and set environment variables.
---

# Installation

## Install the Package

```bash
composer require imsus/laravel-imgproxy:^2.0
```

## Publish Configuration

Publish the config file (`config/laravel-imgproxy.php`):

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

Publish all package resources (config, views, translations, assets) at once:

```bash
php artisan vendor:publish --tag="laravel-imgproxy"
```

Individual tags are also available — `laravel-imgproxy-views` publishes the Blade component templates to `resources/views/vendor/imgproxy`:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-views"
```

## Environment Variables

The default instance reads its connection details from three env vars:

```dotenv
IMGPROXY_URL=https://imgproxy.example.com
IMGPROXY_KEY=943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881
IMGPROXY_SALT=520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5
```

`IMGPROXY_KEY` and `IMGPROXY_SALT` are the hex-encoded values configured on the imgproxy server. When they are absent, URLs are generated unsigned with an `unsafe` signature slot — fine for local development, but configure them for anything exposed to the internet.

Generate a fresh key/salt pair:

```bash
php artisan imgproxy:key
```

## Configuration

The published `config/laravel-imgproxy.php` supports multiple named instances, each with its own server, credentials, signature size, and encoding:

```php
'instances' => [
    'default' => [
        'url' => env('IMGPROXY_URL'),
        'key' => env('IMGPROXY_KEY'),
        'salt' => env('IMGPROXY_SALT'),
        'signature_size' => null,
        'encoding' => 'base64',
    ],

    'staging' => [
        'url' => 'https://imgproxy-staging.example.com',
        'key' => '...',
        'salt' => '...',
        'signature_size' => 16,
        'encoding' => 'base64',
    ],
],
```

Per-instance options:

| Option | Description | Default |
| --- | --- | --- |
| `url` | Base URL of the imgproxy server, no trailing slash | — |
| `key` / `salt` | Hex-encoded HMAC credentials; `null` generates unsigned URLs | `null` |
| `signature_size` | Signature bytes to keep (1–32), matching the server's `IMGPROXY_SIGNATURE_SIZE`; `null` keeps the full digest | `null` |
| `encoding` | Source encoding: `base64` (URL-safe, no padding) or `plain` (percent-encoded source behind a `plain/` prefix) | `'base64'` |

## Presets

Named option sets can be defined in config and shared across instances:

```php
'presets' => [
    'thumb' => [
        'resize' => 'fill',
        'width' => 300,
        'height' => 300,
    ],
],
```

Preset keys match the fluent method names. See [Basic Usage](/guide/usage#presets) for how to apply them from the builder.

## Next Step

- [Basic Usage](/guide/usage) — building URLs, signing, options, and storage disks
