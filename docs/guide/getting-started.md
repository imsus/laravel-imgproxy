# Introduction

Laravel imgproxy is a Laravel package that provides a fluent API for generating URLs with [imgproxy](https://imgproxy.net/), a fast and secure image processing server.

## Features

- **Fluent API** - Chainable methods for building image URLs
- **HMAC Signing** - Secure URL signing out of the box
- **Type-safe Enums** - PHP 8.2+ enums for resize modes and formats
- **Laravel Integration** - Facades, helpers, and config support
- **Blade Components** - Ready-to-use Img and Picture components
- **Visual Effects** - Blur and sharpen adjustments
- **Quality Control** - Fine-tune compression for different formats
- **Well Tested** - 156 tests with workbench integration

## Requirements

- PHP 8.2+
- Laravel 10.x or 11.x
- imgproxy server (self-hosted or cloud)

## Quick Start

```php
use Imsus\ImgProxy\Facades\ImgProxy;
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\OutputExtension;

// Generate a resized, optimized image URL
$url = ImgProxy::url('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->build();
```

Or use the helper function:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->build();
```

## Why imgproxy?

imgproxy is a fast and secure image processing server that provides:

- **Fast Processing** - Optimized for high performance
- **Security** - Prevent image-based attacks
- **Flexibility** - Wide range of processing options
- **Scalability** - Handle high traffic loads
