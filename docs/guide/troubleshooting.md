# Troubleshooting

## Common Issues

### Getting "insecure" URLs instead of signed URLs

**Problem**: URLs are generated without signature
```
http://localhost:8080/insecure/width:300/height:200/...
```

**Solution**: Ensure `IMGPROXY_KEY` and `IMGPROXY_SALT` are set in your `.env` file with valid hex values.

```env
IMGPROXY_KEY=your_hex_key_here
IMGPROXY_SALT=your_hex_salt_here
```

### Invalid hex key/salt errors

**Problem**: `InvalidArgumentException: The key must be a hex-encoded string.`

**Solution**: Generate proper hex keys:
```bash
openssl rand -hex 32
```

### Images not loading / 404 errors

**Problem**: Images return 404 or don't load

**Solutions**:
- Verify imgproxy server is running at the configured endpoint
- Check source image URLs are accessible
- Ensure imgproxy server can reach source URLs (firewall/network issues)

### Poor image quality

**Problem**: Generated images have poor quality

**Solutions**:
- Increase quality setting: `->setQuality(90)`
- Use appropriate output format: `->setExtension(OutputExtension::WEBP)`
- Avoid excessive sharpening: `->setSharpen(1.0)` instead of higher values

### URLs are too long

**Problem**: Generated URLs are very long

**Solutions**:
- Use encoded mode (default) instead of plain mode
- Consider using shorter source URLs (via redirects or aliases)
- If using custom processing strings, simplify them

## Debug Mode

Enable plain URL mode for debugging:

```php
$debugUrl = imgproxy('https://example.com/image.jpg')
    ->setMode(SourceUrlMode::PLAIN)
    ->setWidth(300)
    ->build();

echo $debugUrl;
// Output: http://localhost:8080/signature/width:300/plain/https://example.com/image.jpg@jpg
```

## Validation Errors

The package throws `InvalidArgumentException` for invalid parameters:

| Parameter | Valid Range | Error Message |
|-----------|-------------|---------------|
| Quality | 0-100 | "Quality must be between 0 and 100" |
| DPR | 1-8 | "DPR must be between 1 and 8" |
| Blur | >= 0 | "Blur must be greater than or equal to 0" |
| Sharpen | >= 0 | "Sharpen must be greater than or equal to 0" |
| Width | > 0 | "Width must be greater than 0" |
| Height | > 0 | "Height must be greater than 0" |

## Checking Configuration

Verify your configuration is loaded correctly:

```php
use Imsus\ImgProxy\Facades\ImgProxy;

$config = config('laravel-imgproxy');
dd($config);
```

## Verify imgproxy Connection

Test if your imgproxy server is accessible:

```bash
curl http://localhost:8080/health
```

Or generate a simple URL and test in browser:
```
http://localhost:8080/width:100/height:100/https://example.com/image.jpg
```
