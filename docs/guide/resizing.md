# Resizing

## Basic Resize

Set the width and height for your image:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();
```

## Preserve Aspect Ratio

Use only width or height to maintain the original aspect ratio:

```php
// Width only - height auto-calculated
$url = imgproxy($url)->setWidth(300)->build();

// Height only - width auto-calculated
$url = imgproxy($url)->setHeight(300)->build();
```

## Resize Types

Choose how the image is resized using `ResizeType` enum:

```php
use Imsus\ImgProxy\Enums\ResizeType;

$url = imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FILL)
    ->build();
```

### Available Resize Types

| Type | Description |
|------|-------------|
| `FIT` | Resize keeping aspect ratio to fit dimensions (default) |
| `FILL` | Resize keeping aspect ratio to fill dimensions (crops overflow) |
| `FILL_DOWN` | Same as fill, but maintains requested aspect ratio for smaller images |
| `FORCE` | Resize without keeping aspect ratio (stretches/distorts) |
| `AUTO` | Automatically choose between fit/fill based on orientation |

### Resize Type Examples

```php
// Fit - image fits within bounds, no cropping
imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FIT)
    ->build();

// Fill - image fills bounds, excess is cropped
imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FILL)
    ->build();

// Force - ignore aspect ratio, exact dimensions
imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FORCE)
    ->build();
```

## Gravity

When using `FILL` resize type, you can specify the gravity position to control which part of the image is kept:

```php
use Imsus\ImgProxy\Enums\Gravity;

$url = imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FILL)
    ->setGravity(Gravity::CENTER)
    ->build();
```

### Available Gravity Options

| Gravity | Description |
|---------|-------------|
| `CE` | Center (default) |
| `NO` | North (top) |
| `SO` | South (bottom) |
| `EA` | East (right) |
| `WE` | West (left) |
| `NOEA` | North-East (top-right) |
| `NOWE` | North-West (top-left) |
| `SOEA` | South-East (bottom-right) |
| `SOWE` | South-West (bottom-left) |

### Smart Gravity

Use `auto` gravity for smart cropping based on image content:

```php
$url = imgproxy($url)
    ->setWidth(300)
    ->setHeight(200)
    ->setResizeType(ResizeType::FILL)
    ->setGravity(Gravity::AUTO)
    ->build();
```

## Device Pixel Ratio (DPR)

For high DPI displays (Retina, etc.), set the DPR to generate higher resolution images:

```php
// Standard display (1x)
$url = imgproxy($url)->setWidth(300)->setDpr(1)->build();

// Retina display (2x)
$url = imgproxy($url)->setWidth(300)->setDpr(2)->build();

// High DPI displays (3x)
$url = imgproxy($url)->setWidth(300)->setDpr(3)->build();
```

The actual image size will be `width * dpr` pixels, providing crisp images on high-resolution screens.

## Complete Resize Example

```php
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\Gravity;

$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setGravity(Gravity::CENTER)
    ->setDpr(2)
    ->build();
```
