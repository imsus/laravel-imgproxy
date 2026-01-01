# Installation

Install the package via Composer:

```bash
composer require imsus/laravel-imgproxy
```

## Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-config"
```

Publish the Blade components:

```bash
php artisan vendor:publish --tag="laravel-imgproxy-components"
```

## Configuration

Configure your `.env` file:

```env
IMGPROXY_ENDPOINT=http://localhost:8080
IMGPROXY_KEY=your_hex_key_here
IMGPROXY_SALT=your_hex_salt_here
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=jpeg
```

### Configuration Options

| Option | Description | Default | Required |
|--------|-------------|---------|----------|
| `endpoint` | imgproxy server URL | `http://localhost:8080` | Yes |
| `key` | Hex-encoded signing key for HMAC-SHA256 | `null` | No* |
| `salt` | Hex-encoded signing salt | `null` | No* |
| `default_source_url_mode` | How to encode source URLs | `encoded` | No |
| `default_output_extension` | Default output format | `jpeg` | No |

*The `key` and `salt` are required only if you want to generate signed URLs.

### Generating Keys

Generate a hex-encoded key and salt using:

```bash
openssl rand -hex 32
```

> [!CAUTION]
> The `key` and `salt` must be in hex-encoded format (64 characters each).

## Requirements

- PHP 8.2+
- Laravel 10.x or 11.x
- imgproxy server (self-hosted or cloud)
