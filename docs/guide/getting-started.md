---
title: Getting Started
description: Install Laravel imgproxy and generate your first processed image URL.
---

# Getting Started

[imgproxy](https://imgproxy.net/) is a fast, security-oriented image processing server. You give it a source image and a set of processing options, and it returns a transformed image — resized, cropped, compressed, watermarked, or converted to whatever format you need. It is the kind of tool that quietly handles the image pipeline for an entire application.

Laravel imgproxy is a Laravel package that wraps imgproxy's URL API in a fluent, typed, immutable builder. Instead of hand-crafting and signing URL paths, you write expressive PHP:

```php
use Imsus\LaravelImgproxy\Imgproxy;

$url = Imgproxy::image('https://example.com/image.jpg')
    ->cover(300, 300)
    ->quality(80)
    ->toWebp()
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/q:80/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The package also ships responsive Blade components with LQIP placeholders, a Storage macro for building sources from any disk, and typed enums for every imgproxy option — but we will get to all of that.

## Requirements

- PHP ^8.4
- Laravel 13
- An imgproxy server (v4 recommended)

## Installation

Install the package via Composer:

```bash
composer require imsus/laravel-imgproxy:^2.0
```

Laravel will automatically discover the package's service provider, so nothing else is required to get started.

## Your First URL

```php
Imgproxy::image('https://example.com/image.jpg')
    ->cover(300, 300)
    ->quality(80)
    ->toWebp()
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/q:80/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

Let's break down what happened in the example above. Every imgproxy URL has the same shape:

```text
{server}/{signature}/{options}/{encoded source}
```

- **`unsafe`** — the signature slot. With a key and salt configured, this slot carries an HMAC-SHA256 signature of the path. Without them, it contains the literal string `unsafe`, which is fine for local development but should never be used in production.
- **`rs:fill:300:300`** — the option segment for `cover(300, 300)`. `cover` is an *intent method*: it says "crop to fill a 300×300 box" in domain terms and compiles down to the resize `fill` segment, cropping any overflow. `fit()` is its sibling, saying "fit within the box" and compiling to `rs:fit`.
- **`q:80`** — compression quality.
- **`f:webp`** — output format, published by `toWebp()`.
- **`aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc`** — the source URL, URL-safe base64 encoded without padding.

These high-level *intent methods* are the headline. When an intent method isn't wired up — or you know exactly which wire option you want — the builder also exposes one typed method per imgproxy processing option (`resize()`, `crop()`, `gravity()`, `format()`, …) as a precise escape hatch. Every input is validated, and `withOption()` appends a raw segment verbatim for anything newer.

Because the signature covers the exact path that follows it, you never have to think about signature or encoding details — the builder handles all of that.

## Next Steps

- [Installation](/guide/installation) — publish config, set environment variables, generate signing keys
- [Basic Usage](/guide/usage) — the facade and helper, signing, options, presets, and Storage disks
- [Blade Components](/guide/blade-components) — responsive `<img>` and `<picture>` tags
