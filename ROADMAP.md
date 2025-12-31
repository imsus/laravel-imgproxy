# Laravel-ImgProxy Roadmap to v1.0.0

## Version Progression

```
v0.4.0 → v0.5.0 → v0.6.0 → v0.7.0 → v0.8.0 → v0.9.0 → v1.0.0
```

---

## v0.5.0 - Package Completeness

### Goals
- Complete missing package documentation files
- Clean up empty directories

### Tasks
- [ ] `SECURITY.md` - Security policy
- [ ] `CONTRIBUTING.md` - Contribution guidelines
- [ ] `CODE_OF_CONDUCT.md` - Community standards
- [ ] Remove or populate empty `resources/views/` and `database/factories/`

---

## v0.6.0 - Gravity & Positioning

### Goals
- Add gravity system for crop/fill positioning

### New Files
- `src/Enums/Gravity.php`

### New Methods
| Method | ImgProxy | Description |
|--------|----------|-------------|
| `gravity(Gravity $g)` | `g:` | Set gravity |
| `crop(int $w, int $h, ?Gravity $g = null)` | `c:w:h:g` | Crop to size |

### Config Changes
- Add `'default_gravity' => 'ce'`

---

## v0.7.0 - Core Processing

### Goals
- Add commonly needed processing options

### New Methods
| Method | ImgProxy | Description |
|--------|----------|-------------|
| `padding(int $all)` | `pd:` | Add padding |
| `background(string $hex)` | `bg:#hex` | Background color |
| `autoRotate(bool $b = true)` | `ar:` | Auto-orient |
| `rotate(int $deg)` | `rot:` | Manual rotate |
| `stripMetadata(bool $b = true)` | `sm:` | Remove EXIF |
| `trim(int $threshold = 10)` | `trim:` | Trim borders |
| `pixelate(int $size)` | `pix:` | Pixelate |

---

## v0.8.0 - Watermarking

### Goals
- Add watermark support

### New Methods
| Method | ImgProxy | Description |
|--------|----------|-------------|
| `watermark(string $url)` | `wm:` | Watermark image |
| `watermarkOpacity(float $o)` | `wm:o` | Opacity 0-1 |
| `watermarkPosition(Gravity $g)` | `wm:g` | Position |
| `watermarkScale(float $s)` | `wm:s` | Scale factor |

---

## v0.9.0 - DX Enhancements

### Goals
- Improve developer experience

### New Features
- **Blade Component**: `<x-imgproxy src="..." width="200" />`
- **Builder Clone**: `$url->copy()->width(300)->build()`
- **Response Factory**: `ImgProxyResponse::make($url)->stream()`

### Tasks
- [ ] Create `src/Components/ImgProxyComponent.php`
- [ ] Add view namespace registration
- [ ] Add `clone()` method to Builder
- [ ] Create response factory class

---

## v1.0.0 - Stable Release

### Goals
- Lock in API, declare stability
- Prepare forPackagist promotion

### Tasks
- [ ] Comprehensive integration tests
- [ ] Performance benchmarks
- [ ] Update `README.md` with examples
- [ ] Add CHANGELOG.md
- [ ] Tag v1.0.0 release
