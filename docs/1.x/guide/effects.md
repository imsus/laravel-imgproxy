# Visual Effects

## Blur

Apply a gaussian blur effect to the image:

```php
$url = imgproxy($url)
    ->width(500)
    ->height(300)
    ->blur(2.0)
    ->build();
```

The blur value is the sigma of the gaussian function. Recommended values are between 0.1 and 10.

## Sharpen

Apply sharpening to enhance image details:

```php
$url = imgproxy($url)
    ->width(500)
    ->height(300)
    ->sharpen(1.5)
    ->build();
```

The sharpen value controls the amount of sharpening. Recommended values are between 0.1 and 3.

## Combining Effects

Chain multiple effects together:

```php
$url = imgproxy('https://example.com/photo.jpg')
    ->width(500)
    ->height(300)
    ->blur(2.0)
    ->sharpen(1.5)
    ->quality(85)
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
imgproxy($url)->blur(1.0)->build();

// Strong blur for privacy/obfuscation
imgproxy($url)->blur(5.0)->build();

// Subtle sharpening for product photos
imgproxy($url)->sharpen(0.5)->build();

// Strong sharpening for detailed images
imgproxy($url)->sharpen(2.0)->build();
```

## Complete Example

```php
$url = imgproxy('https://example.com/photo.jpg')
    ->width(800)
    ->height(600)
    ->cover()
    ->blur(2.0)
    ->sharpen(1.0)
    ->quality(85)
    ->build();
```
