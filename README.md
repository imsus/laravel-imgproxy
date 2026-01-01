<img width="1280" height="640" alt="Laravel imgproxy banner" src="https://github.com/user-attachments/assets/71b48db1-7d28-426f-b803-f4f69b3b70b2" />

# Laravel imgproxy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/imsus/laravel-imgproxy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)

A Laravel package for [imgproxy](https://imgproxy.net/) integration. Generate optimized, signed image URLs with a fluent API.

## Use Case

Imagine you run an e-commerce platform with thousands of product images. Pages load slowly, cart abandonment rises, and hosting costs climb because every image is served at full resolution. The marketing team needs the same hero image in multiple aspect ratios for different campaigns, but waiting for designers to resize manually slows everything down.

This package solves both problems. Images are resized and compressed on-the-fly—thumbnail, medium, and hero versions generated from a single source. WebP and AVIF formats are served automatically based on browser support, reducing bandwidth by up to 50%. The fluent API lets you chain resizing, quality, and effects in a single readable line, so developers ship faster and users get faster pages.

## Why This Package?

You can call imgproxy's raw API directly, but you'd repeat boilerplate code across every project: URL signing logic, configuration loading, enum types, validation, and error handling. This package wraps all that in a clean Laravel package. You get type-safe enums for resize modes and formats, fluent chainable methods that read like sentences, and Laravel-specific conveniences like facades, helpers, and Blade components. The 99.4% test coverage means you can trust it in production. If you're already using Laravel, this feels native—no learning curve, just `imgproxy()->build()` and you're done.

## Quick Glance

### Helper Function

```php
imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->setBlur(2.0)
    ->setSharpen(1.0)
    ->setDpr(2)
    ->build();
// Output: http://imgproxy.local/signature/width:800/height:600/.../image.webp
```

### Facade

```php
use Imsus\ImgProxy\Facades\ImgProxy;

ImgProxy::url('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->build();
// Output: http://imgproxy.local/signature/width:800/height:600/plain/https://example.com/image.jpg@jpeg
```

### Blade Components

```blade
{{-- Single image --}}
<x-imgproxy-img
    src="https://example.com/image.jpg"
    alt="Product name"
    :width="300"
    :height="200"
    resize-type="fill"
    format="webp"
    :quality="85"
/>

{{-- Output: <img src="http://imgproxy.local/signature/width:300/height:200/resizing_type:fill/..." alt="Product name" loading="lazy"> --}}

{{-- Responsive with multiple formats --}}
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    alt="Hero banner"
    :width="1200"
    :height="600"
    :formats="['avif', 'webp', 'jpeg']"
    resize-type="fill"
    :quality="85"
/>

{{-- Output: <picture><source srcset="..." type="image/avif"><source srcset="..." type="image/webp"><img ...></picture> --}}
```

## Quick Start

```bash
composer require imsus/laravel-imgproxy
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

```php
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\OutputExtension;

$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->build();
```

## Documentation

- **[Getting Started](docs/guide/getting-started.md)** - Introduction and requirements
- **[Installation](docs/guide/installation.md)** - Setup and configuration
- **[Usage Guide](docs/guide/usage.md)** - Basic usage patterns
- **[Resizing](docs/guide/resizing.md)** - Resize modes, gravity, DPR
- **[Quality & Format](docs/guide/quality.md)** - Output settings
- **[Visual Effects](docs/guide/effects.md)** - Blur and sharpen
- **[Blade Components](docs/guide/blade-components.md)** - Img and Picture components
- **[Advanced Usage](docs/guide/advanced-usage.md)** - Laravel integration patterns
- **[API Reference](docs/reference/api.md)** - Complete method reference
- **[Enums Reference](docs/reference/enums.md)** - Type-safe enums
- **[Security](docs/guide/security.md)** - Best practices
- **[Testing](docs/contribute/testing.md)** - Running tests

## Features

- **Fluent API** - Chainable methods for building image URLs
- **HMAC Signing** - Secure URL signing with configurable key/salt
- **Blade Components** - Ready-to-use Img and Picture components
- **Type Safe** - PHP 8.2+ enums for all options
- **Well Tested** - 99.4% test coverage

## Commands

```bash
composer test          # Run tests
composer test-coverage # Run tests with coverage
composer format        # Format code
composer start         # Start workbench server
```

## License

MIT License. See [LICENSE](LICENSE.md) for details.
