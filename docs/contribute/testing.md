# Testing

## Running Tests

```bash
# Run all tests
composer test

# Run only unit tests
composer test --filter=Unit

# Run only integration tests
composer test --filter=WorkbenchIntegrationTest

# Run specific test file
composer test --filter=UrlGenerationTest

# Run with coverage
composer test-coverage
```

## Test Structure

The package includes **156 comprehensive tests** with **376 assertions** covering:

- **Unit Tests** (130 tests) - Organized by functionality:
  - `UrlGenerationTest` - Signed URLs, helper, fluent methods
  - `ValidationTest` - DPR, quality, blur, sharpen
  - `S3UrlTest` - S3 URL handling
  - `ExceptionTest` - Invalid hex key/salt exceptions
  - `EffectsTest` - Quality and effects in URL building
  - `GravityEnumTest` - Gravity enum cases and defaults
  - `GravityMethodsTest` - gravity() and crop() methods
  - `ImgTest` - Img blade component
  - `PictureTest` - Picture blade component
- **Integration Tests** (15 tests) - Laravel environment, HTTP endpoints, service provider registration
- **Architecture Tests** (7 tests) - Code structure, security, conventions
- **Visual Tests** - Browser-based real image processing validation
- **Performance Tests** - URL generation speed benchmarks

## Interactive Testing with Workbench

The package includes a comprehensive workbench environment for interactive testing:

```bash
# Build and start the workbench server
composer start

# Or build separately and serve
composer build
php vendor/bin/testbench serve
```

Once the server is running (typically at `http://localhost:8000`), you can access:

### API Test Endpoints

| Endpoint | Description |
|----------|-------------|
| `/imgproxy-test/` | JSON overview of all available tests |
| `/imgproxy-test/basic` | Basic URL generation testing |
| `/imgproxy-test/effects` | Quality and visual effects testing |
| `/imgproxy-test/formats` | Format conversion (JPEG, PNG, WebP, AVIF) |
| `/imgproxy-test/resize` | Different resize types comparison |
| `/imgproxy-test/facade-vs-helper` | Compare facade and helper output |
| `/imgproxy-test/config` | Configuration validation |
| `/imgproxy-test/error-handling` | Error scenarios testing |
| `/imgproxy-test/performance` | Performance benchmarks |

### Visual Testing

- **Visual Test Suite**: `http://localhost:8000/imgproxy-visual-test` - Complete browser-based visual testing

The visual test page includes:
- Real Image Processing - See actual ImgProxy results with sample images
- Quality Comparison - Side-by-side quality levels (30%, 70%, 95%)
- Format Comparison - Visual differences between JPEG, PNG, WebP, AVIF
- Resize Types Demo - Visual behavior of fit, fill, force, auto modes
- Effects Showcase - Blur, sharpen effects
- Complex Processing - Portrait enhancement and vintage effects
- High DPI Examples - Standard vs 2x DPI comparisons

## Sample Test Responses

### Basic Test Response

```json
{
    "original": "https://picsum.photos/800/600",
    "processed": "http://localhost:8080/signed-url/width:400/height:300/...",
    "test": "basic_url_generation"
}
```

### Performance Test Response

```json
{
    "urls_generated": 100,
    "duration_seconds": 0.0089,
    "urls_per_second": 1123.6,
    "test": "performance"
}
```

## Writing Tests

### Basic URL Test

```php
it('generates a basic URL', function () {
    $url = imgproxy('https://example.com/image.jpg')
        ->setWidth(300)
        ->setHeight(200)
        ->build();

    expect($url)->toContain('width:300');
    expect($url)->toContain('height:200');
});
```

### Signed URL Test

```php
it('generates signed URLs', function () {
    $url = imgproxy('https://example.com/image.jpg')
        ->setWidth(300)
        ->build();

    // URLs should not contain 'insecure'
    expect($url)->not->toContain('/insecure/');
});
```

### Validation Test

```php
it('throws exception for invalid quality', function () {
    expect(fn() => imgproxy('https://example.com/image.jpg')
        ->setQuality(150)
        ->build())->toThrow(InvalidArgumentException::class);
});
```
