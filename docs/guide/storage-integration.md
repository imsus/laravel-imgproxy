---
title: Storage Integration
description: Build imgproxy URLs from Laravel Storage disks with automatic public and private disk handling.
---

# Storage Integration

In most real applications, images do not live on random URLs — they live on Storage disks. The package integrates with Laravel's filesystem layer, so you can build imgproxy sources from any disk without thinking about whether the disk is public or private.

## How It Works

- **Public disks** yield the disk's `url()` — a plain, permanent URL.
- **Private disks** yield a pre-signed `temporaryUrl()` — a time-limited URL that imgproxy can use to fetch the file.

A disk is treated as private when it implements `providesTemporaryUrls()` **and** its config does not explicitly set `'visibility' => 'public'`. Disks that omit the visibility key (common for S3-style drivers) produce pre-signed URLs.

## The `imgproxy()` Macro

The `imgproxy()` macro is available on any `Storage::disk()` call and returns a builder:

```php
use Illuminate\Support\Facades\Storage;

// Public disk -> url()
Storage::disk('public')->imgproxy('images/photo.jpg')
    ->width(800)
    ->format(Format::Webp)
    ->url();
```

### Private Disks and Pre-signed URLs

Private disks generate a pre-signed `temporaryUrl()` with a **5-minute default** expiration:

```php
// Private disk (S3) -> pre-signed temporaryUrl(), 5 minutes by default
Storage::disk('s3')->imgproxy('products/image.jpg', 3600)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
```

Pass an expiration in seconds as the second argument. The URL is valid for that duration, and imgproxy fetches the source within the window.

## The `Builder::disk()` Method

The builder exposes an equivalent `->disk()` method when you need to set the disk inline without the macro:

```php
imgproxy()->image('unused')->disk('s3', 'products/image.jpg', 3600)->width(800)->url();
```

The third argument accepts:

- **`int`** — seconds from now (e.g. `3600` for one hour).
- **`DateTimeInterface`** — an absolute expiration time.

```php
use Carbon\Carbon;

// Absolute expiration
imgproxy()->image('unused')
    ->disk('s3', 'products/image.jpg', Carbon::now()->addHour())
    ->width(800)
    ->url();
```

## The Visibility Rule

The detection logic checks two conditions:

1. The disk's `providesTemporaryUrls()` returns `true` (e.g. S3, GCS).
2. The disk config does **not** have `'visibility' => 'public'`.

If both are true, the disk is private and yields pre-signed URLs. If either condition fails, the disk is public and yields plain `url()`.

This means S3 disks without an explicit `'visibility' => 'public'` in `config/filesystems.php` produce pre-signed URLs — even if the bucket is publicly accessible. Add `'visibility' => 'public'` to the disk config to force plain URLs:

```php
// config/filesystems.php
's3' => [
    'driver' => 's3',
    'visibility' => 'public', // Forces url() instead of temporaryUrl()
    // ...
],
```

## Examples

### Public disk with format conversion

```php
use Illuminate\Support\Facades\Storage;
use Imsus\LaravelImgproxy\Enums\Format;

$url = Storage::disk('public')->imgproxy('images/photo.jpg')
    ->width(800)
    ->format(Format::Webp)
    ->url();
```

### Private S3 disk with custom expiration

```php
$url = Storage::disk('s3')->imgproxy('products/image.jpg', 3600)
    ->resize(ResizeType::Fill, 800, 600)
    ->url();
```

### Builder disk method with absolute expiration

```php
use Carbon\Carbon;

$url = imgproxy()->image('unused')
    ->disk('s3', 'products/image.jpg', Carbon::now()->addHours(2))
    ->width(800)
    ->url();
```
