# Release Notes v0.10.0

**Release Date**: 2026-01-01

## Breaking Changes

### Removed Pro-Only Features

The following features required ImgProxy Pro and have been removed for compatibility with the free version:

- `setBrightness()` - Removed
- `setContrast()` - Removed
- `setSaturation()` - Removed
- `watermarkUrl()` - Removed

If you were using these methods, your code will need to be updated.

### Rotation Now Uses Enum

The `rotate()` method now accepts a `Rotation` enum instead of an integer:

```php
// Before
->rotate(90)

// After
->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_90)
```

Available values:
- `Rotation::DEG_0`
- `Rotation::DEG_90`
- `Rotation::DEG_180`
- `Rotation::DEG_270`

### Background Color Format

The `background()` method no longer includes the `#` prefix in the URL:

```php
// Before - generated bg:#FF5733
// After - generates bg:FF5733
```

## Bug Fixes

### ImgProxy Helper Instance

Fixed an issue where the `imgproxy()` helper function was reusing the same instance, causing state to persist between calls. Each call now creates a fresh instance.

### Watermark Position

Fixed `watermark()` method rejecting `null` for the position parameter.

### Invalid Rotation Angles

Fixed rotation accepting invalid angles like 45 degrees. ImgProxy only supports 0, 90, 180, 270 degree rotations.

## New Features

### Rotation Enum

Added `Imsus\ImgProxy\Enums\Rotation` enum for type-safe rotation values.

### Workbench Improvements

- Added `workbench/composer.json` for proper workbench setup
- Updated visual test page with working watermark examples
- Fixed all examples to use only free-tier compatible features

## Development

### Tests

- Updated all tests to reflect API changes
- Removed validation tests for removed methods
- Fixed integration tests for workbench endpoints

## Upgrade Guide

1. Replace any `setBrightness()`, `setContrast()`, `setSaturation()` calls with alternatives or remove them
2. Replace `watermarkUrl()` calls - custom watermark URLs require ImgProxy Pro
3. Update `rotate()` calls to use the new `Rotation` enum
4. Update any code expecting `#` prefix in background color URLs

```bash
composer update imsus/laravel-imgproxy
```
