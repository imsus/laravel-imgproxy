---
title: Installation
description: Install Laravel imgproxy, publish the configuration, and set your environment variables.
---

# Installation

## Install the Package

Install the package via Composer:

```bash
composer require imsus/laravel-imgproxy:^2.0
```

## Publish the Configuration

While the package works out of the box, you will probably want to publish its configuration file so you can register additional imgproxy instances and presets:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

This will create a `config/laravel-imgproxy.php` file in your application. If you would like to publish all package resources at once — config, views, and translations — you may use the combined tag instead:

```bash
php artisan vendor:publish --tag="laravel-imgproxy"
```

Individual tags are also available. For example, `laravel-imgproxy-views` publishes the Blade component templates to `resources/views/vendor/imgproxy` so you can customize them:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-views"
```

## Environment Variables

The default instance reads its connection details from three environment variables:

```dotenv
IMGPROXY_URL=https://imgproxy.example.com
IMGPROXY_KEY=943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881
IMGPROXY_SALT=520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5
```

`IMGPROXY_KEY` and `IMGPROXY_SALT` are the hex-encoded values configured on your imgproxy server. When they are absent, generated URLs are unsigned and use the `unsafe` signature slot — perfectly fine for local development, but you should configure them for anything exposed to the internet.

To generate a fresh key and salt pair, use the `imgproxy:key` command:

```bash
php artisan imgproxy:key
```

The command prints the pair as environment lines that you can copy straight into your `.env` file.

## Configuring Instances

The published configuration file supports multiple named instances, each with its own server, credentials, signature size, and source encoding:

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

| Option | Description | Default |
| --- | --- | --- |
| `url` | Base URL of the imgproxy server, without a trailing slash | — |
| `key` / `salt` | Hex-encoded HMAC credentials; `null` generates unsigned URLs | `null` |
| `signature_size` | Signature bytes to keep (1–32), matching the server's `IMGPROXY_SIGNATURE_SIZE`; `null` keeps the full digest | `null` |
| `encoding` | Source encoding: `base64` (URL-safe, no padding) or `plain` (percent-encoded source behind a `plain/` prefix) | `'base64'` |

You may learn more about working with named instances in the [Advanced Usage](/guide/advanced-usage) documentation.

## Defining Presets

Presets are named sets of processing options that you can share across instances and reuse throughout your application:

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

## Next Steps

- [Basic Usage](/guide/usage) — building URLs, signing, options, presets, and Storage disks
