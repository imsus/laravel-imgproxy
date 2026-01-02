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
    ->processing('rs:fill:400:300:1/rt:fit/q:85/bl:2.0')
    ->build();
```

## Cache Busting

Use the `v()` method to invalidate cached images when you update the source image:

```php
$url = imgproxy('https://example.com/image.jpg')
    ->width(300)
    ->height(200)
    ->v(2)  // Increment this value to force refresh
    ->build();
```

## Fallback URL

Set a fallback image for when the source URL is missing or invalid:

```php
// Using config fallback
$url = imgproxy('https://example.com/missing.jpg')
    ->width(300)
    ->height(200)
    ->build();

// Per-request fallback
$url = imgproxy('https://example.com/missing.jpg')
    ->width(300)
    ->height(200)
    ->fallback('https://example.com/placeholder.jpg')
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
    return "<?php echo imgproxy($expression)->width(150)->height(150)->cover()->build(); ?>";
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
            ->width(150)
            ->height(150)
            ->cover()
            ->webp()
            ->quality(85)
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
                'small' => imgproxy($this->avatar)->width(50)->height(50)->build(),
                'medium' => imgproxy($this->avatar)->width(150)->height(150)->build(),
                'large' => imgproxy($this->avatar)->width(300)->height(300)->build(),
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
            ->width($size)
            ->height($size)
            ->cover()
            ->webp()
            ->quality(85)
            ->sharpen(0.5)
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
                ->width($width)
                ->height(intval($width * 0.75)) // 4:3 aspect ratio
                ->cover()
                ->webp()
                ->quality(85)
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
<img src="{{ imgproxy($image)->width(800)->build() }}"
     srcset="{{ $srcsetString }}"
     sizes="(max-width: 768px) 100vw, 50vw"
     alt="Responsive image">
```

## Image Processing Recipes

### Portrait Enhancement

```php
$enhancedPortrait = imgproxy($portrait)
    ->width(600)
    ->height(800)
    ->cover()
    ->sharpen(0.8)
    ->quality(92)
    ->build();
```

### Product Photography

```php
$productClean = imgproxy($product)
    ->width(800)
    ->height(800)
    ->contain()
    ->sharpen(1.5)
    ->quality(95)
    ->webp()
    ->build();
```
