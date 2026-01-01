# Security Best Practices

## Environment Configuration

Use strong, unique keys for your environment:

```bash
# Generate strong keys
IMGPROXY_KEY=$(openssl rand -hex 32)
IMGPROXY_SALT=$(openssl rand -hex 32)

# Use HTTPS in production
IMGPROXY_ENDPOINT=https://imgproxy.yoursite.com
```

### Environment Variables

```env
# Never commit keys to version control
IMGPROXY_ENDPOINT=https://imgproxy.yoursite.com
IMGPROXY_KEY=your_secure_hex_key_here
IMGPROXY_SALT=your_secure_hex_salt_here

# Use encoded mode for additional security
IMGPROXY_DEFAULT_SOURCE_URL_MODE=encoded
IMGPROXY_DEFAULT_OUTPUT_EXTENSION=webp
```

## URL Validation

Always validate source URLs before processing to prevent abuse:

```php
class ImageProcessor
{
    private array $allowedDomains = [
        'your-cdn.com',
        'storage.googleapis.com',
        's3.amazonaws.com',
    ];

    public function processImage(string $imageUrl): string
    {
        $parsedUrl = parse_url($imageUrl);

        if (!in_array($parsedUrl['host'], $this->allowedDomains)) {
            throw new InvalidArgumentException('Image domain not allowed');
        }

        return imgproxy($imageUrl)
            ->setWidth(800)
            ->setHeight(600)
            ->setQuality(85)
            ->build();
    }
}
```

## Signature Security

### Key Rotation

Regularly rotate your signing keys:

```bash
# Generate new keys
openssl rand -hex 32  # New key
openssl rand -hex 32  # New salt

# Update .env file
# Deploy to production
# Remove old keys
```

### Separate Keys per Environment

Use different keys for each environment:

```env
# Development
IMGPROXY_KEY=dev_key_here
IMGPROXY_SALT=dev_salt_here

# Production
IMGPROXY_KEY=prod_key_here
IMGPROXY_SALT=prod_salt_here
```

## Rate Limiting

Consider implementing rate limiting on your imgproxy server to prevent abuse:

```php
// In your imgproxy configuration
rate_limit:
  period: 1
  limit: 1000  # requests per period
```

## Input Validation

The package validates all inputs, but additional validation may be needed:

```php
public function processImage(string $imageUrl, ?int $width, ?int $height): string
{
    // Validate URL format
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid URL format');
    }

    // Validate dimensions
    if ($width !== null && ($width < 1 || $width > 5000)) {
        throw new InvalidArgumentException('Width must be between 1 and 5000');
    }

    if ($height !== null && ($height < 1 || $height > 5000)) {
        throw new InvalidArgumentException('Height must be between 1 and 5000');
    }

    return imgproxy($imageUrl)
        ->setWidth($width)
        ->setHeight($height)
        ->build();
}
```

## Secure Endpoint Configuration

Configure your imgproxy server securely:

```yaml
# imgproxy.yml
signature:
  key: your_signature_key
  salt: your_signature_salt
  allowed_sources:
    - cdn.yoursite.com
    - s3.amazonaws.com
  download_timeout: 10
  max_download_size: 50000000  # 50MB
```

## Audit Logging

Monitor image processing activity:

```php
class AuditedImageProcessor
{
    public function processImage(string $imageUrl, array $options = []): string
    {
        $startTime = microtime(true);

        $url = imgproxy($imageUrl)
            ->setWidth($options['width'] ?? null)
            ->setHeight($options['height'] ?? null)
            ->setQuality($options['quality'] ?? 85)
            ->build();

        $duration = microtime(true) - $startTime;

        Log::channel('imgproxy')->info('Image processed', [
            'url' => $imageUrl,
            'options' => $options,
            'duration' => $duration,
            'result_url' => $url,
        ]);

        return $url;
    }
}
```
