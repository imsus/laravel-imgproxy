---
title: Getting Started
description: Install Laravel imgproxy and generate your first processed image URL.
---

# Getting Started

Laravel imgproxy is a Laravel package for [imgproxy](https://imgproxy.net/). It generates signed, processed image URLs through an immutable fluent builder, renders responsive `<img>` and `<picture>` tags with LQIP placeholders, and builds sources from Storage disks.

## Requirements

- PHP ^8.4
- Laravel 13
- An imgproxy server (v4 recommended)

## Installation

Install via Composer:

```bash
composer require imsus/laravel-imgproxy:^2.0
```

## First URL

```php
use Imsus\LaravelImgproxy\Imgproxy;
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\ResizeType;

$url = Imgproxy::url('https://example.com/image.jpg')
    ->resize(ResizeType::Fill, 300, 300)
    ->quality(80)
    ->format(Format::Webp)
    ->url();

// https://imgproxy.example.com/unsafe/rs:fill:300:300/q:80/f:webp/aHR0cHM6Ly9leGFtcGxlLmNvbS9pbWFnZS5qcGc
```

The `unsafe` signature slot in the URL means no signing key is configured. This is fine for local development, but configure [HMAC credentials](/guide/installation) for anything exposed to the internet.

## Next Steps

- [Installation](/guide/installation) — publish config, set env vars, generate signing keys
- [Basic Usage](/guide/usage) — building URLs, signing, options, presets, and storage disks
