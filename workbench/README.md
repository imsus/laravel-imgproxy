# Laravel imgproxy Package - Workbench Testing

This workbench provides a complete testing environment for the ImgProxy Laravel package, including API endpoints, visual tests, and integration testing.

> **Note:** This workbench is for testing the [imsus/laravel-imgproxy](README.md) package.

## Table of Contents

- [Quick Start](#quick-start)
- [Test Endpoints](#test-endpoints)
- [Integration Tests](#integration-tests)
- [Visual Testing](#visual-testing)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Laravel 11+

### Start the Workbench

```bash
# Build and start the workbench
composer start

# Or build separately and then start
composer build
php vendor/bin/testbench serve
```

The server runs at `http://localhost:8000`.

## Test Endpoints

### API Test Endpoints

| Endpoint                          | Description                               |
| --------------------------------- | ----------------------------------------- |
| `/imgproxy-test/`                 | JSON overview of all tests                |
| `/imgproxy-test/basic`            | Basic URL generation                      |
| `/imgproxy-test/effects`          | Quality and visual effects                |
| `/imgproxy-test/formats`          | Format conversion (JPEG, PNG, WebP, AVIF) |
| `/imgproxy-test/resize`           | Different resize types                    |
| `/imgproxy-test/facade-vs-helper` | Compare facade and helper                 |
| `/imgproxy-test/config`           | Configuration testing                     |
| `/imgproxy-test/error-handling`   | Error scenarios                           |
| `/imgproxy-test/performance`      | Performance benchmarks                    |

### Visual Test Page

- **Visual Tests**: `http://localhost:8000/imgproxy-visual-test` - Browser-based visual testing with real images

## Integration Tests

```bash
# Run all tests
composer test

# Run only workbench integration tests
composer test --filter=WorkbenchIntegrationTest
```

## Visual Testing

The visual test page includes:

- Real image processing with Picsum Photos
- Quality comparison (30%, 70%, 95%)
- Format comparison (JPEG, PNG, WebP, AVIF)
- Resize type comparisons
- Visual effects (blur, sharpen, saturation)
- Complex processing (portrait, vintage)
- High DPI support (1x vs 2x)

## Configuration

The workbench uses `workbench/config/imgproxy.php`:

```php
return [
    'endpoint' => env('IMGPROXY_ENDPOINT', 'http://localhost:8080'),
    'key' => env('IMGPROXY_KEY'),
    'salt' => env('IMGPROXY_SALT'),
    'default_source_url_mode' => env('IMGPROXY_DEFAULT_SOURCE_URL_MODE', 'encoded'),
    'default_output_extension' => env('IMGPROXY_DEFAULT_OUTPUT_EXTENSION', 'jpeg'),
];
```

### Environment Variables

| Variable                            | Description           | Default                 |
| ----------------------------------- | --------------------- | ----------------------- |
| `IMGPROXY_ENDPOINT`                 | imgproxy server URL   | `http://localhost:8080` |
| `IMGPROXY_KEY`                      | HMAC signing key      | -                       |
| `IMGPROXY_SALT`                     | HMAC salt             | -                       |
| `IMGPROXY_DEFAULT_SOURCE_URL_MODE`  | URL encoding mode     | `encoded`               |
| `IMGPROXY_DEFAULT_OUTPUT_EXTENSION` | Default output format | `jpeg`                  |

## Troubleshooting

### Server Won't Start

```bash
# Clear caches
php artisan optimize:clear

# Rebuild workbench
composer dump-autoload
composer build
```

### Tests Fail

```bash
# Run with verbose output
composer test -vvv
```

### imgproxy Connection Issues

Ensure imgproxy server is running at the configured endpoint:
```bash
curl http://localhost:8080/health
```

## Development Notes

- **Routes**: `WorkbenchServiceProvider.php`
- **Views**: `workbench/resources/views/`
- **Tests**: `tests/WorkbenchIntegrationTest.php`
- **Sample Images**: Picsum Photos service

---

For package documentation, see [README.md](README.md).
