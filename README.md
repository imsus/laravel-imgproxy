<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://github.com/user-attachments/assets/bb2e37e0-bc75-4e67-b1c8-772fc4b489ea">
  <source media="(prefers-color-scheme: light)" srcset="https://github.com/user-attachments/assets/71b48db1-7d28-426f-b803-f4f69b3b70b2">
  <img alt="Laravel imgproxy banner" src="https://github.com/user-attachments/assets/71b48db1-7d28-426f-b803-f4f69b3b70b2" style="width: 100%; aspect-ratio:1280/640">
</picture>

# Laravel imgproxy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/imsus/laravel-imgproxy/actions?query=workflow%3Aci+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/imsus/laravel-imgproxy.svg?style=flat-square)](https://packagist.org/packages/imsus/laravel-imgproxy)

A Laravel package for [imgproxy](https://imgproxy.net/) integration. Generate optimized, signed image URLs with a fluent API.

## Use Case

Imagine you run an e-commerce platform with thousands of product images. Pages load slowly, cart abandonment rises, and hosting costs climb because every image is served at full resolution. The marketing team needs the same hero image in multiple aspect ratios for different campaigns, but waiting for designers to resize manually slows everything down.

This package solves both problems. Images are resized and compressed on-the-fly—thumbnail, medium, and hero versions generated from a single source. WebP and AVIF formats are served automatically based on browser support, reducing bandwidth by up to 50%. The fluent API lets you chain resizing, quality, and effects in a single readable line, so developers ship faster and users get faster pages.

## Why This Package?

You can call imgproxy's raw API directly, but you'd repeat boilerplate code across every project: URL signing logic, configuration loading, enum types, validation, and error handling. This package wraps all that in a clean Laravel package. You get type-safe enums for resize modes and formats, fluent chainable methods that read like sentences, and Laravel-specific conveniences like facades, helpers, and Blade components. The 99.4% test coverage means you can trust it in production. If you're already using Laravel, this feels native—no learning curve, just `imgproxy()->build()` and you're done.

## Features

- **Fluent API** - Chainable methods for building image URLs
- **HMAC Signing** - Secure URL signing with configurable key/salt
- **Blade Components** - Ready-to-use Img and Picture components
- **Type Safe** - PHP 8.2+ enums for all options
- **Well Tested** - 99.4% test coverage

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

{{-- Output:
    <img src="http://imgproxy.local/signature/width:300/height:200/resizing_type:fill/..." alt="Product name" loading="lazy">
--}}

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

{{-- Output:
<picture>
    <source srcset="..." type="image/avif">
    <source srcset="..." type="image/webp">
    <img ...>
</picture>
--}}
```

## Quick Start

### Prerequisites

Before using this package, you need to have [imgproxy](https://imgproxy.net/) set up and running. You can either:
- Use a hosted imgproxy service
- Run imgproxy locally using Docker
- Deploy imgproxy to your preferred cloud platform

Make sure you have your imgproxy URL and signing credentials ready.

### Installation

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

Full documentation available at **[imsus.github.io/laravel-imgproxy](https://imsus.github.io/laravel-imgproxy/)**:

- **[Getting Started](https://imsus.github.io/laravel-imgproxy/guide/getting-started)** - Introduction and requirements
- **[Installation](https://imsus.github.io/laravel-imgproxy/guide/installation)** - Setup and configuration
- **[Usage Guide](https://imsus.github.io/laravel-imgproxy/guide/usage)** - Basic usage patterns
- **[Resizing](https://imsus.github.io/laravel-imgproxy/guide/resizing)** - Resize modes, gravity, DPR
- **[Quality & Format](https://imsus.github.io/laravel-imgproxy/guide/quality)** - Output settings
- **[Visual Effects](https://imsus.github.io/laravel-imgproxy/guide/effects)** - Blur and sharpen
- **[Blade Components](https://imsus.github.io/laravel-imgproxy/guide/blade-components)** - Img and Picture components
- **[Advanced Usage](https://imsus.github.io/laravel-imgproxy/guide/advanced-usage)** - Laravel integration patterns
- **[API Reference](https://imsus.github.io/laravel-imgproxy/reference/api)** - Complete method reference
- **[Enums Reference](https://imsus.github.io/laravel-imgproxy/reference/enums)** - Type-safe enums
- **[Security](https://imsus.github.io/laravel-imgproxy/guide/security)** - Best practices
- **[Testing](https://imsus.github.io/laravel-imgproxy/contribute/testing)** - Running tests

## Configuration

Publish the config file and configure via `.env`:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

```env
IMGPROXY_ENDPOINT=http://localhost:8080
IMGPROXY_KEY=
IMGPROXY_SALT=
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=jpeg
IMGPROXY_DEFAULT_GRAVITY=ce
```

| Option                              | Description                                                    | Default                 |
| ----------------------------------- | -------------------------------------------------------------- | ----------------------- |
| `IMGPROXY_ENDPOINT`                 | Your imgproxy server URL                                       | `http://localhost:8080` |
| `IMGPROXY_KEY`                      | HMAC signing key                                               | `null`                  |
| `IMGPROXY_SALT`                     | HMAC salt                                                      | `null`                  |
| `IMGPROXY_DEFAULT_SOURCE_URL_MODE`  | Default source URL mode (`encoded` or `raw`)                   | `encoded`               |
| `IMGPROXY_DEFAULT_OUTPUT_EXTENSION` | Default output format (`jpeg`, `png`, `webp`, `avif`, `gif`)   | `jpeg`                  |
| `IMGPROXY_DEFAULT_GRAVITY`          | Default gravity (`ce`, `no`, `so`, `ea`, `we`, `ce`, `c`, `f`) | `ce`                    |

If no key/salt is configured, URLs will be generated unsigned.

## Troubleshooting

### Signature verification failed

- Verify `IMGPROXY_KEY` and `IMGPROXY_SALT` match your imgproxy server configuration
- Ensure encoding (hex vs base64) is consistent between your app and imgproxy server

### URLs not generating

- Check that `IMGPROXY_ENDPOINT` is accessible from your application
- Validate the source URL is publicly accessible or allowlisted in imgproxy

### Images loading slowly

- Enable DPR for retina displays: `->setDpr(2)`
- Use `OutputExtension::AVIF` for best compression
- Consider lower quality for smaller file sizes: `->setQuality(75)`

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and breaking changes.

## Commands

```bash
composer test          # Run tests
composer test-coverage # Run tests with coverage
composer format        # Format code
composer start         # Start workbench server
```

## License

MIT License. See [LICENSE](LICENSE.md) for details.

Laravel is a trademark of https://laravel.com/legal/trademark.

imgproxy is a trademark of https://imgproxy.net/.
