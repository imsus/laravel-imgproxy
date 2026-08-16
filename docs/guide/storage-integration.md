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

## Materializing Processed Images

`toStorage()` is the terminal counterpart of `url()`: instead of returning a URL, it fetches the processed image from imgproxy and writes it to a destination disk, returning a `StoredImage` representation of the stored file.

```php
use Imsus\LaravelImgproxy\Enums\Format;

$image = imgproxy()->image('https://example.com/photo.jpg')
    ->width(800)
    ->format(Format::Webp)
    ->toStorage('s3', 'processed/photo.webp');
```

The imgproxy URL is built from the current source and processing options; the response body is streamed to the disk, so large images never load fully into memory. An existing file at the destination path is overwritten. Extra write options (visibility, Content-Type, metadata) pass through to the disk write:

```php
$image = imgproxy()->image('https://example.com/photo.jpg')
    ->toStorage('s3', 'processed/photo.webp', ['visibility' => 'public']);
```

The returned `StoredImage` carries the disk and path, and produces URLs with the same public/private rule as sources:

```php
$image->disk();    // 's3'
$image->path();    // 'processed/photo.webp'
$image->name();    // 'photo.webp'

// Public destination disk -> plain object URL
$image->url();

// Private destination disk -> pre-signed temporaryUrl(), 5 minutes by default
$image->url(3600);

// The destination disk adapter, for advanced operations
$image->adapter()->delete($image->path());
```

`(string) $image` is the same URL, so stored images work directly in Blade: `{{ $image }}`.

### Failure behavior

- When imgproxy responds with a non-success status (anything outside 2xx, including redirects), an `ImgproxyStorageException` is thrown **before** any disk write, with the HTTP status and a snippet of the response body in the message.
- When the disk write fails, the original error is wrapped in the same exception type. This covers both throwing disks and disks configured with `'throw' => false`, which report failures by returning `false` instead.
- The destination disk must exist; a missing disk throws Laravel's usual `InvalidArgumentException` before any HTTP request is made.
- The fetch uses a 30-second timeout that covers the whole request, including the streamed body transfer; for very large renders or slow links, raise it via `Http::timeout()` configuration on the request or the HTTP client defaults.

## Use Cases

### Converting images between storages

The storage-to-storage pipeline: the raw image stays on its origin disk, imgproxy converts it, and the result is written to a different disk:

::: tip
imgproxy is a conversion tool that *serves* the processed bytes — it has no feature to upload results to your storage. `toStorage()` is the bridge: it fetches the converted output and persists it, so imgproxy acts as a pure conversion service in the middle of your pipeline.
:::

```php
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\ResizeType;

$image = Storage::disk('origin')->imgproxy('raw/photo.jpg')
    ->resize(ResizeType::Fill, 800, 600)
    ->format(Format::Webp)
    ->toStorage('processed', 'converted/photo.webp');

$image->url(); // serve from the processed disk
```

### Materializing variants at upload time

Generate format and size variants once, when the original is uploaded, instead of converting on every request. The stored images are durable — they survive imgproxy instance changes and key rotation, and imgproxy never sits in the request path afterward:

```php
use Imsus\LaravelImgproxy\Enums\Format;

foreach ([300, 600, 1200] as $width) {
    Storage::disk('origin')->imgproxy('raw/photo.jpg')
        ->width($width)
        ->format(Format::Webp)
        ->toStorage('cdn', "variants/photo-{$width}.webp", ['visibility' => 'public']);
}
```

### Batch conversion with queued jobs

`toStorage()` is synchronous — one conversion per call, blocking until the bytes are written. For large batches, run the conversions in queued jobs:

::: warning
Do **not** build the source URL before queueing a job. A private origin disk yields a pre-signed source URL that expires (5 minutes by default); a job that runs later fetches an expired source and fails. Resolve the disk inside the job instead, so the pre-signed URL is fresh at run time.
:::

```php
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Imsus\LaravelImgproxy\Enums\Format;

class ConvertImageJob implements ShouldQueue
{
    public function __construct(private readonly string $path) {}

    public function handle(): void
    {
        Storage::disk('origin')->imgproxy($this->path)
            ->format(Format::Webp)
            ->toStorage('processed', 'converted/'.$this->path.'.webp');
    }
}
```
