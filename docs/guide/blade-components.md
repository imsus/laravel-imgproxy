---
title: Blade Components
description: Render responsive img and picture tags with srcsets, LQIP placeholders, and format negotiation.
---

# Blade Components

The package ships two Blade components: `<x-imgproxy-img>` for a single `<img>` with responsive srcsets, and `<x-imgproxy-picture>` for format negotiation with a fallback image.

## `<x-imgproxy-img>`

Renders an `<img>` with a srcset built from width or DPR candidates, sizes, an LQIP placeholder, lazy loading by default, alt text, and class passthrough.

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

Outputs:

```html
<img
    src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/..."
    srcset="https://imgproxy.example.com/unsafe/rs:fill:300:300/w:320/... 320w, ..."
    sizes="(min-width: 1024px) 50vw, 100vw"
    loading="lazy"
    alt="A photo"
    class="rounded shadow"
>
```

### Attributes

| Attribute | Type | Default | Description |
| --- | --- | --- | --- |
| `src` | `string` | *(required)* | Source image URL |
| `disk` | `string?` | `null` | Storage disk name (alternative to `src`) |
| `path` | `string?` | `null` | File path on the disk (used with `disk`) |
| `preset` | `string?` | `null` | Named preset from config, composed before per-URL overrides |
| `widths` | `array\|string?` | `null` | Srcset width candidates (`w` descriptors) |
| `dprs` | `array\|string?` | `null` | Srcset DPR candidates (`x` descriptors) |
| `sizes` | `string?` | `null` | HTML `sizes` attribute |
| `placeholder` | `bool` | `false` | Enable LQIP placeholder in `src` |
| `alt` | `string?` | `null` | Alt text for accessibility |
| `loading` | `string` | `lazy` | Loading strategy (`lazy` or `eager`) |
| `class` | `string` | — | Class passthrough to the `<img>` element |

### Width and DPR candidates

`widths` and `dprs` build the `srcset` attribute with `w` or `x` descriptors respectively. They are **mutually exclusive** — a single srcset cannot mix both descriptor types.

Each accepts an array or a comma-separated string:

```blade
{{-- Array syntax --}}
<x-imgproxy-img src="..." :widths="[320, 640, 1280]" />

{{-- String syntax --}}
<x-imgproxy-img src="..." widths="320, 640, 1280" />

{{-- DPR candidates with x descriptors --}}
<x-imgproxy-img src="..." :dprs="[1, 2, 3]" />
```

### Storage disk alternative to `src`

Instead of a `src` URL, pass `disk` and `path` to build the source from a [Storage disk](/guide/storage-integration):

```blade
<x-imgproxy-img
    disk="public"
    path="images/photo.jpg"
    :widths="[640, 1280]"
    sizes="100vw"
    alt="A photo"
/>
```

### Preset

The `preset` attribute composes a [named preset](/guide/usage) from config. Options chained after the preset (via per-URL overrides) win.

### Placeholder

When `placeholder` is set, the `src` attribute carries a tiny blurred webp (`w:16`, `bl:8`, `f:webp`) for blur-up previews. The full-size image stays reachable through the srcset.

### Loading

`loading` defaults to `lazy`. Pass `loading="eager"` for above-the-fold images.

## `<x-imgproxy-picture>`

Renders a `<picture>` with one `<source>` per format and a fallback `<img>`. Every format except the last becomes a `<source>` element; the last format is the fallback `<img>`.

```blade
<x-imgproxy-picture
    src="https://example.com/image.jpg"
    :widths="[640, 1280]"
    :formats="['avif', 'webp', 'jpg']"
    sizes="100vw"
    alt="A photo"
/>
```

Outputs:

```html
<picture>
    <source srcset="https://imgproxy.example.com/unsafe/f:avif/w:640/... 640w, ..." type="image/avif">
    <source srcset="https://imgproxy.example.com/unsafe/f:webp/w:640/... 640w, ..." type="image/webp">
    <img src="https://imgproxy.example.com/unsafe/f:jpg/..." loading="lazy" alt="A photo">
</picture>
```

### Attributes

`<x-imgproxy-picture>` accepts every attribute from `<x-imgproxy-img>` above, plus:

| Attribute | Type | Default | Description |
| --- | --- | --- | --- |
| `formats` | `array\|string?` | `['avif', 'webp', 'jpg']` | Output formats in priority order |

### How `formats` works

The `formats` attribute determines the render order:

- **All formats except the last** become `<source>` elements, each with its own `srcset`.
- **The last format** is the fallback `<img>`.

The default `['avif', 'webp', 'jpg']` produces AVIF and WebP sources with a JPG fallback. Browsers that support AVIF get that first, then WebP, then fall back to JPG.

You can pass formats as an array or a comma-separated string:

```blade
{{-- Array syntax --}}
<x-imgproxy-picture src="..." :formats="['avif', 'webp', 'jpg']" />

{{-- String syntax --}}
<x-imgproxy-picture src="..." formats="avif, webp, jpg" />
```

Supported format values: `jpg`, `png`, `webp`, `avif`, `gif`, `ico`, `svg`, `bmp`, `tiff`, `heic`, `jxl`.

### Placeholder with `<picture>`

When `placeholder` is set on `<x-imgproxy-picture>`, the fallback `<img>` `src` carries the LQIP placeholder, and the full-size image stays reachable through the srcset on the `<img>` element.
