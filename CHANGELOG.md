# Changelog

All notable changes to `laravel-imgproxy` will be documented in this file.

## v0.8.0 - 2025-12-31

### Added

2 new watermark methods:

| Method                                                     | Format                        | Description                                  |
|------------------------------------------------------------|-------------------------------|----------------------------------------------|
| watermark($opacity, $position, $xOffset, $yOffset, $scale) | wm:opacity:position:x:y:scale | Built-in watermark with configurable options |
| watermarkUrl($url)                                         | wmu:<base64-encoded-url>      | Custom watermark from URL                    |

watermark() Parameters:

- $opacity (float, default 0.5) - Watermark opacity (0.0-1.0)
- $position (Gravity, default CENTER) - Position using existing Gravity enum
- $xOffset (int, default 0) - X axis offset in pixels
- $yOffset (int, default 0) - Y axis offset in pixels
- $scale (float, default 0) - Scale factor relative to result image

Gravity Positions Supported:

- ce (center), n (north), s (south), e (east), w (west)
- ne (north-east), nw (north-west), se (south-east), sw (south-west)

### Testing

- 25 new unit tests in `tests/Unit/WatermarkTest.php`
- Workbench API endpoint: `/imgproxy-test/watermark`
- Visual tests in `/imgproxy-visual-test`

### Stats

- Tests: 123 total (was 107)
- Assertions: 307 total (was 265)

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.7.0...v0.8.0

## v0.7.0 - 2025-12-31

### Added

7 new core processing methods:

| Method                       | Parameter | ImgProxy Key | Description               |
| ---------------------------- | --------- | ------------ | ------------------------- |
| padding(int $padding)        | int       | pd:          | Add padding to images     |
| background(string $hex)      | string    | bg:#hex      | Set background color      |
| autoRotate(bool $autoRotate) | bool      | ar:          | Auto-orient based on EXIF |
| rotate(int $degrees)         | int       | rot:         | Rotate image by degrees   |
| stripMetadata(bool $strip)   | bool      | sm:          | Remove EXIF/metadata      |
| trim(int $threshold)         | int       | trim:        | Trim image borders        |
| pixelate(int $size)          | int       | pix:         | Pixelate image            |

### Testing

- 35 new unit tests in `tests/Unit/CoreProcessingTest.php`
- Workbench API endpoint: `/imgproxy-test/core-processing`
- Visual tests in `/imgproxy-visual-test`
- Parallel test execution enabled

### Stats

- Tests: 107 total (was 71)
- Assertions: 295 total (was 195)

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.6.0...v0.7.0

## v0.6.0 - 2025-12-31

### Added

- Gravity System - New Gravity enum with 9 positions:
  
  - ce (center), n (north), s (south), e (east), w (west)
  - ne, se, sw, nw (corners)
  
- `setGravity(Gravity $g)` - Set gravity for fill/crop positioning `(g:)`
  
- `crop(int $w, int $h, ?Gravity $g)` - Crop with gravity `(c:w:h:g)`
  
- `default_gravity` config - Via `IMGPROXY_DEFAULT_GRAVITY` env
  
- Visual workbench tests - Live demos for gravity/crop
  

### Changed

- Test reorganization - Split `ImgProxyTest.php` into focused files:
  - `UrlGenerationTest`, `ValidationTest`, `S3UrlTest`, `ExceptionTest`, `EffectsTest`
  

### Fixed

- CI workflow - Removed explicit nesbot/carbon to avoid security advisory conflicts
- PHPStan config - Include Larastan extension

### Stats

- 71 tests (was 39)
- 195 assertions (was 145)
- All tests passing

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.5.0...v0.6.0

## v0.5.0 - 2025-12-31

### What's Changed

* chore(deps): bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot[bot] in https://github.com/imsus/laravel-imgproxy/pull/5
* chore(deps): bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/imsus/laravel-imgproxy/pull/6
* Support s3:// source URLs by @yoomarket in https://github.com/imsus/laravel-imgproxy/pull/12 continued on #13
* chore(deps): bump actions/checkout from 4 to 6 by @dependabot[bot] in https://github.com/imsus/laravel-imgproxy/pull/10
* chore(deps): bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/imsus/laravel-imgproxy/pull/9

### New Contributors

* @yoomarket made their first contribution in https://github.com/imsus/laravel-imgproxy/pull/12

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.4.0...v0.5.0

## v0.4.0 - 2025-07-18

### 🚀 Major Features

- **Visual Effects Engine**: New methods for blur, sharpen, brightness, contrast, and saturation adjustments
- **Quality Control**: Fine-grained compression control (0-100) for optimal file sizes
- **Enhanced Validation**: Comprehensive parameter bounds checking with clear error messages
- **Fluent API Extensions**: All new methods support method chaining for clean, readable code

### 🎨 New Methods

- `setQuality(int $quality)` - Control compression quality (0-100)
- `setBlur(float $sigma)` - Apply blur effects
- `setSharpen(float $sigma)` - Enhance image sharpness
- `setBrightness(int $brightness)` - Adjust brightness (-255 to 255)
- `setContrast(float $contrast)` - Modify contrast levels
- `setSaturation(float $saturation)` - Control color saturation

### 🧪 Testing & Development

- **39 Comprehensive Tests**: 26 unit + 13 integration + 7 architecture tests
- **Interactive Workbench**: Visual testing environment with real image processing
- **Performance Validated**: >1000 URLs/second generation speed
- **Visual Test Suite**: Browser-based validation with sample images

### 📚 Documentation Overhaul

- Complete API reference with parameter details and examples
- Laravel integration patterns (Blade directives, Eloquent accessors, API resources)
- Performance optimization strategies and responsive image examples
- Security best practices and comprehensive troubleshooting guide

### 🔧 Developer Experience

- Interactive testing endpoints for visual validation
- Enhanced error messages with specific validation details
- Development configuration with testing commands
- Real-time visual effects preview in workbench

### 📈 Technical Improvements

- Type-safe parameter validation for all new methods
- Backward compatibility maintained with existing APIs
- Enhanced fluent interface design
- Laravel 10+ compatibility preserved

### 🎯 Use Cases

Perfect for e-commerce product images, user avatars, photo galleries, responsive images, and any application requiring dynamic image processing with visual enhancement capabilities.

**Upgrade Note**: Fully backward compatible - existing code continues to work without changes.

## v0.3.0 - 2025-02-27

### v0.3.0

This update includes several behind-the-scenes improvements to enhance the stability and performance of the Laravel ImgProxy integration.  We've also corrected a minor spelling error.

#### What's Changed

* chore(deps): bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/imsus/laravel-imgproxy/pull/2
* chore(deps): bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/imsus/laravel-imgproxy/pull/3
* Typo by @felixdorn in https://github.com/imsus/laravel-imgproxy/pull/1

#### New Contributors

* @dependabot made their first contribution in https://github.com/imsus/laravel-imgproxy/pull/2
* @felixdorn made their first contribution in https://github.com/imsus/laravel-imgproxy/pull/1

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.2.1...v0.3.0

## v0.2.1 - 2024-09-25

**Error Handling Enhancement**

- We've improved the error handling mechanism by adding a try-catch block.
- Now, if an InvalidArgumentException occurs during validateSourceUrl(), the system will fallback to the original source URL.

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.2.0...v0.2.1

## v0.2.0 - 2024-09-25

**Device Pixel Ratio Adjustment**

- Introduced `setDpr` method to customize device pixel ratio
- Enables developers to optimize image quality for various screen resolutions

**Full Changelog**: https://github.com/imsus/laravel-imgproxy/compare/v0.1.1...v0.2.0
