# Laravel Storage Integration

Laravel imgproxy provides seamless integration with Laravel's Storage facade, automatically handling both public and private disks.

## Quick Start

Use the `imgproxy()` macro on any storage disk:

```php
use Illuminate\Support\Facades\Storage;

// Public disk
$url = Storage::disk('public')->imgproxy('avatars/user.jpg')
    ->width(300)
    ->height(200)
    ->webp()
    ->build();

// Private disk (S3) - automatically generates presigned URLs
$url = Storage::disk('s3')->imgproxy('products/image.jpg')
    ->width(800)
    ->height(600)
    ->cover()
    ->build();
```

## How It Works

The macro automatically detects the disk type:

- **Public disks** (local, S3 with public access): Uses `disk->url()` to get the file URL
- **Private disks** (S3, Google Cloud Storage): Uses `disk->temporaryUrl()` to generate presigned URLs

This means you can use the same API regardless of disk visibility:

```php
// Works with any disk
$url = Storage::disk($anyDisk)->imgproxy($path)->width(500)->build();
```

## Using fromStorage() Directly

For more control, you can use the `ImgProxy::fromStorage()` static method:

```php
use Imsus\ImgProxy\ImgProxy;
use Illuminate\Support\Facades\Storage;

$disk = Storage::disk('s3');
$path = 'products/photo.jpg';

$url = ImgProxy::fromStorage($disk, $path)
    ->width(1200)
    ->height(800)
    ->webp()
    ->quality(85)
    ->build();
```

## Examples

### E-commerce Product Images

```php
use Illuminate\Support\Facades\Storage;

// Get product image from private S3 bucket
$productImage = Storage::disk('s3')
    ->imgproxy("products/{$product->id}.jpg")
    ->width(800)
    ->height(800)
    ->cover()
    ->webp()
    ->build();
```

### User Avatar with Fallback

```php
$avatar = Storage::disk('s3')
    ->imgproxy("avatars/{$user->id}.jpg")
    ->width(200)
    ->height(200)
    ->cover()
    ->webp()
    ->build();
```

### Responsive Image Gallery

```php
$sizes = [400, 800, 1200];
$gallery = [];

foreach ($sizes as $size) {
    $gallery[$size] = Storage::disk('cdn')
        ->imgproxy("gallery/{$image->id}.jpg")
        ->width($size)
        ->webp()
        ->build();
}
```

## Configuration

No additional configuration is required. The integration uses your existing disk configurations in `config/filesystems.php`.

For private disks (S3, etc.), ensure your `temporaryUrl` is properly configured:

```php
// config/filesystems.php
'disks' => [
    's3' => [
        'driver' => 's3',
        // ... other S3 config
        'temporary_url' => true, // Required for presigned URLs
    ],
],
```
