# Blade Components

The package includes two Blade components inspired by Next.js Image component for easy use in Laravel templates.

## Img Component

The `Img` component generates an optimized image tag with imgproxy processing.

```blade
<x-imgproxy-img
    src="https://example.com/image.jpg"
    alt="Description"
    :width="300"
    :height="200"
    resize-type="fill"
    format="webp"
    :quality="75"
    :dpr="2"
    gravity="ce"
    lazy
    sizes="(max-width: 768px) 100vw, 50vw"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | Required | Source image URL |
| `alt` | `string` | Required | Alt text for accessibility |
| `width` | `int?` | `null` | Target width in pixels |
| `height` | `int?` | `null` | Target height in pixels |
| `resizeType` | `ResizeType?` | `null` | Resize mode (fit, fill, force, etc.) |
| `format` | `OutputExtension?` | `null` | Output format (jpeg, png, webp, avif) |
| `quality` | `int` | `75` | Compression quality (0-100) |
| `dpr` | `int?` | `null` | Device pixel ratio |
| `gravity` | `Gravity?` | `null` | Gravity position for crop/fill |
| `lazy` | `bool` | `true` | Enable lazy loading |
| `sizes` | `string?` | `null` | HTML sizes attribute |

### Example

```blade
<x-imgproxy-img
    src="{{ $product->image_url }}"
    alt="{{ $product->name }}"
    :width="800"
    :height="600"
    resize-type="fill"
    format="webp"
    :quality="85"
/>
```

## Picture Component

The `Picture` component generates a `<picture>` element with multiple format sources for responsive images.

```blade
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    alt="Description"
    :width="800"
    :height="600"
    :formats="['webp', 'avif', 'jpeg']"
    resize-type="fill"
    :quality="75"
    lazy
    sizes="(max-width: 768px) 100vw, 50vw"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | Required | Source image URL |
| `alt` | `string` | Required | Alt text for accessibility |
| `width` | `int?` | `null` | Target width in pixels |
| `height` | `int?` | `null` | Target height in pixels |
| `formats` | `array` | `['webp', 'avif', 'jpeg']` | Output formats in priority order |
| `resizeType` | `ResizeType?` | `null` | Resize mode (fit, fill, force, etc.) |
| `quality` | `int` | `75` | Compression quality (0-100) |
| `dpr` | `int?` | `null` | Device pixel ratio |
| `gravity` | `Gravity?` | `null` | Gravity position for crop/fill |
| `lazy` | `bool` | `true` | Enable lazy loading |
| `sizes` | `string?` | `null` | HTML sizes attribute |

### Example

```blade
<x-imgproxy-picture
    src="{{ $hero->image }}"
    alt="{{ $hero->title }}"
    :width="1200"
    :height="600"
    :formats="['avif', 'webp', 'jpeg']"
    resize-type="fill"
    :quality="85"
    sizes="(max-width: 1200px) 100vw, 1200px"
/>
```

This generates a `<picture>` element with `<source>` tags for AVIF, WebP, and JPEG formats, allowing browsers to choose the best supported format.
