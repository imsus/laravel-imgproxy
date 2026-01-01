# Advanced Usage

## Source URL Mode

Control how the source URL is encoded in the generated URL:

```php
use Imsus\ImgProxy\Enums\SourceUrlMode;

// Encoded mode (default) - Base64 encode source URL
$url = imgproxy($url)
    ->setMode(SourceUrlMode::ENCODED)
    ->build();

// Plain mode - Use plain text URL (for debugging)
$url = imgproxy($url)
    ->setMode(SourceUrlMode::PLAIN)
    ->build();
```

## Custom Processing

For advanced use cases, you can set raw processing options:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->setProcessing('rs:fill:400:300:1/rt:fit/q:85/bl:2.0')
    ->build();
```

## Laravel Integration

### Blade Directives

Create custom Blade directives for common use cases:

```php
// In AppServiceProvider::boot()
use Illuminate\Support\Facades\Blade;

Blade::directive('imgproxy', function ($expression) {
    return "<?php echo imgproxy($expression)->build(); ?>";
});

Blade::directive('avatar', function ($expression) {
    return "<?php echo imgproxy($expression)->setWidth(150)->setHeight(150)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)->build(); ?>";
});
```

```blade
{{-- Usage in Blade templates --}}
<img src="@imgproxy($product->image)" alt="Product">
<img src="@avatar($user->avatar)" alt="User Avatar">
```

### Eloquent Accessors

Add image processing to Eloquent models:

```php
class User extends Model
{
    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return '/default-avatar.png';
        }

        return imgproxy($this->avatar)
            ->setWidth(150)
            ->setHeight(150)
            ->setResizeType(ResizeType::FILL)
            ->setExtension(OutputExtension::WEBP)
            ->setQuality(85)
            ->build();
    }
}
```

### API Resources

Use in API resources for consistent image URLs:

```php
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => [
                'small' => imgproxy($this->avatar)->setWidth(50)->setHeight(50)->build(),
                'medium' => imgproxy($this->avatar)->setWidth(150)->setHeight(150)->build(),
                'large' => imgproxy($this->avatar)->setWidth(300)->setHeight(300)->build(),
            ],
        ];
    }
}
```

## Common Patterns

### Avatar Processing

```php
class UserAvatar
{
    public static function generate(string $imageUrl, int $size = 150): string
    {
        return imgproxy($imageUrl)
            ->setWidth($size)
            ->setHeight($size)
            ->setResizeType(ResizeType::FILL)
            ->setExtension(OutputExtension::WEBP)
            ->setQuality(85)
            ->setSharpen(0.5)
            ->build();
    }
}

// Usage
$avatarUrl = UserAvatar::generate($user->profile_image, 200);
```

### Responsive Images

Generate multiple image sizes for responsive images:

```php
class ResponsiveImage
{
    public static function generateSrcset(string $imageUrl, array $sizes): array
    {
        $srcset = [];

        foreach ($sizes as $width) {
            $url = imgproxy($imageUrl)
                ->setWidth($width)
                ->setHeight(intval($width * 0.75)) // 4:3 aspect ratio
                ->setResizeType(ResizeType::FILL)
                ->setExtension(OutputExtension::WEBP)
                ->setQuality(85)
                ->build();

            $srcset[] = "{$url} {$width}w";
        }

        return $srcset;
    }
}

// Usage
$sizes = [400, 800, 1200, 1600];
$srcset = ResponsiveImage::generateSrcset($image, $sizes);
$srcsetString = implode(', ', $srcset);
```

```blade
<img src="{{ imgproxy($image)->setWidth(800)->build() }}"
     srcset="{{ $srcsetString }}"
     sizes="(max-width: 768px) 100vw, 50vw"
     alt="Responsive image">
```

## Image Processing Recipes

### Portrait Enhancement

```php
$enhancedPortrait = imgproxy($portrait)
    ->setWidth(600)
    ->setHeight(800)
    ->setResizeType(ResizeType::FILL)
    ->setSharpen(0.8)
    ->setQuality(92)
    ->build();
```

### Product Photography

```php
$productClean = imgproxy($product)
    ->setWidth(800)
    ->setHeight(800)
    ->setResizeType(ResizeType::FIT)
    ->setSharpen(1.5)
    ->setQuality(95)
    ->setExtension(OutputExtension::WEBP)
    ->build();
```
