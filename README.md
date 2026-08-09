<div align="center">
    <h1>Laravel imgproxy</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/v/imsus/laravel-imgproxy.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/php-v/imsus/laravel-imgproxy.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://badge.laravel.cloud/badge/imsus/laravel-imgproxy?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/imsus/laravel-imgproxy/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/imsus/laravel-imgproxy/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/imsus/laravel-imgproxy"><img src="https://img.shields.io/packagist/dt/imsus/laravel-imgproxy.svg?style=flat-square" alt="Total Downloads"></a>
</p>

imgproxy integration for Laravel

## Installation

You can install the package via Composer:

```bash
composer require imsus/laravel-imgproxy
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="laravel-imgproxy"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

### Publishing the Component Views

```bash
php artisan vendor:publish --tag="laravel-imgproxy-views"
```

The component templates publish to `resources/views/vendor/imgproxy` and override the package defaults.

## Usage

### Blade Components

`<x-imgproxy-img>` renders an `<img>` with a srcset built from width or DPR candidates, sizes, an LQIP placeholder, lazy loading by default, alt text, and class passthrough:

```blade
<x-imgproxy-img
    src="https://example.com/image.jpg"
    :widths="[320, 640, 1280]"
    sizes="(min-width: 1024px) 50vw, 100vw"
    preset="thumb"
    placeholder
    alt="A photo"
    class="rounded shadow"
/>
```

```html
<img src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/..." srcset="https://imgproxy.example.com/unsafe/rs:fill/w:300/h:300/w:320/... 320w, ..." loading="lazy" alt="A photo" class="rounded shadow">
```

- `widths` and `dprs` build the srcset (`w` or `x` descriptors); they are mutually exclusive and accept an array or a comma-separated string.
- `preset` composes a named preset from config before per-URL overrides.
- `placeholder` swaps the `src` to a tiny blurred webp of the same source and keeps the full image reachable through the srcset.
- `loading` defaults to `lazy`; pass `loading="eager"` to opt out.
- `src` may be replaced with `disk` and `path` to build the source from a Storage disk, matching the builder's `->disk()` behavior.

`<x-imgproxy-picture>` renders a `<picture>` with one `<source>` per format and a fallback `<img>`. Every format except the last becomes a source; the last is the fallback image (AVIF and WebP sources with a JPG fallback by default):

```blade
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    :widths="[640, 1280]"
    :formats="['avif', 'webp', 'jpg']"
    sizes="100vw"
    alt="A photo"
/>
```

```html
<picture>
    <source srcset="https://imgproxy.example.com/unsafe/f:avif/w:640/... 640w, ..." type="image/avif">
    <source srcset="https://imgproxy.example.com/unsafe/f:webp/w:640/... 640w, ..." type="image/webp">
    <img src="https://imgproxy.example.com/unsafe/f:jpg/..." loading="lazy" alt="A photo">
</picture>
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Laravel imgproxy! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Imam Susanto](https://github.com/imsus)
- [All Contributors](../../contributors)

## License

Laravel imgproxy is open-sourced software licensed under the [MIT license](LICENSE.md).
