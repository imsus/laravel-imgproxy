# Visual Effects

## Blur

Apply a gaussian blur effect to the image:

```php
$url = imgproxy($url)
    ->setWidth(500)
    ->setHeight(300)
    ->setBlur(2.0)
    ->build();
```

The blur value is the sigma of the gaussian function. Recommended values are between 0.1 and 10.

## Sharpen

Apply sharpening to enhance image details:

```php
$url = imgproxy($url)
    ->setWidth(500)
    ->setHeight(300)
    ->setSharpen(1.5)
    ->build();
```

The sharpen value controls the amount of sharpening. Recommended values are between 0.1 and 3.

## Combining Effects

Chain multiple effects together:

```php
$url = imgproxy('https://example.com/photo.jpg')
    ->setWidth(500)
    ->setHeight(300)
    ->setBlur(2.0)
    ->setSharpen(1.5)
    ->setQuality(85)
    ->build();
```

## Effect Guidelines

| Effect | Value Range | Use Case |
|--------|-------------|----------|
| Blur | 0.1 - 10 | Backgrounds, privacy, aesthetics |
| Sharpen | 0.1 - 3 | Product photos, detailed images |

### Effect Examples

```php
// Gentle blur for background images
imgproxy($url)->setBlur(1.0)->build();

// Strong blur for privacy/obfuscation
imgproxy($url)->setBlur(5.0)->build();

// Subtle sharpening for product photos
imgproxy($url)->setSharpen(0.5)->build();

// Strong sharpening for detailed images
imgproxy($url)->setSharpen(2.0)->build();
```

## Complete Example

```php
$url = imgproxy('https://example.com/photo.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setBlur(2.0)
    ->setSharpen(1.0)
    ->setQuality(85)
    ->build();
```
