# Basic Usage

## Helper Function

The easiest way to generate an image URL is using the `imgproxy()` helper function:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();
```

## Facade

You can also use the facade for static access:

```php
use Imsus\ImgProxy\Facades\ImgProxy;

$url = ImgProxy::url('https://example.com/image.jpg')
    ->setWidth(300)
    ->setHeight(200)
    ->build();
```

## Complete Example

Here's a complete example with all available options:

```php
use Imsus\ImgProxy\Facades\ImgProxy;
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\OutputExtension;

$url = ImgProxy::url('https://example.com/image.jpg')
    ->setWidth(800)
    ->setHeight(600)
    ->setResizeType(ResizeType::FILL)
    ->setExtension(OutputExtension::WEBP)
    ->setQuality(85)
    ->setDpr(2)
    ->setBlur(2.0)
    ->setSharpen(1.5)
    ->build();
```

## Result

The `build()` method returns the complete, signed imgproxy URL:

```php
"http://localhost:8080/signed-url/width:800/height:600/quality:85/..."
```

## Method Chaining

The fluent API allows you to chain methods in any order:

```php
$url = imgproxy($image)
    ->setBlur(2.0)
    ->setWidth(800)
    ->setQuality(85)
    ->setSharpen(1.0)
    ->setHeight(600)
    ->build();
```

## Error Handling

The package includes comprehensive validation and will throw `InvalidArgumentException` for invalid parameters:

```php
try {
    $url = imgproxy('invalid-url')
        ->setQuality(150)  // Invalid: > 100
        ->build();
} catch (InvalidArgumentException $e) {
    // Handle validation error
    echo $e->getMessage(); // "Quality must be between 0 and 100"
}
```

For invalid URLs, the package gracefully returns the original URL instead of throwing an exception.
