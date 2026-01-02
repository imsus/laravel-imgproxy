<?php

namespace Imsus\ImgProxy;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Enums\Rotation;
use Imsus\ImgProxy\Enums\SourceUrlMode;
use InvalidArgumentException;

class ImgProxy
{
    private string $endpoint;

    private string $key;

    private string $salt;

    private string $source_url;

    private SourceUrlMode $source_url_mode;

    private OutputExtension $default_output_extension;

    private ?OutputExtension $overridden_extension = null;

    /** @var array<string, float|int|string> */
    private array $options = [];

    private ?string $processing_options = null;

    private bool $use_short_options = false;

    private ?string $fallback_url = null;

    private const SHORT_OPTIONS = [
        'resize' => 'rs',
        'size' => 's',
        'resizing_type' => 'rt',
        'width' => 'w',
        'height' => 'h',
        'min-width' => 'mw',
        'min-height' => 'mh',
        'zoom' => 'z',
        'dpr' => 'dpr',
        'enlarge' => 'el',
        'extend' => 'ex',
        'extend_aspect_ratio' => 'exar',
        'gravity' => 'g',
        'crop' => 'c',
        'trim' => 't',
        'padding' => 'pd',
        'auto_rotate' => 'ar',
        'rotate' => 'rot',
        'background' => 'bg',
        'blur' => 'bl',
        'sharpen' => 'sh',
        'pixelate' => 'pix',
        'watermark' => 'wm',
        'strip_metadata' => 'sm',
        'keep_copyright' => 'kcr',
        'strip_color_profile' => 'scp',
        'enforce_thumbnail' => 'eth',
        'quality' => 'q',
        'format_quality' => 'fq',
        'max_bytes' => 'mb',
        'format' => 'f',
        'skip_processing' => 'skp',
        'raw' => 'raw',
        'cachebuster' => 'cb',
        'expires' => 'exp',
        'filename' => 'fn',
        'return_attachment' => 'att',
        'preset' => 'pr',
        'max_src_resolution' => 'msr',
        'max_src_file_size' => 'msfs',
        'max_animation_frames' => 'maf',
        'max_animation_frame_resolution' => 'mafr',
        'max_result_dimension' => 'mrd',
    ];

    private const FULL_OPTIONS = [
        'rs' => 'resize',
        's' => 'size',
        'rt' => 'resizing_type',
        'w' => 'width',
        'h' => 'height',
        'mw' => 'min-width',
        'mh' => 'min-height',
        'z' => 'zoom',
        'dpr' => 'dpr',
        'el' => 'enlarge',
        'ex' => 'extend',
        'exar' => 'extend_aspect_ratio',
        'g' => 'gravity',
        'c' => 'crop',
        't' => 'trim',
        'pd' => 'padding',
        'ar' => 'auto_rotate',
        'rot' => 'rotate',
        'bg' => 'background',
        'bl' => 'blur',
        'sh' => 'sharpen',
        'pix' => 'pixelate',
        'wm' => 'watermark',
        'sm' => 'strip_metadata',
        'kcr' => 'keep_copyright',
        'scp' => 'strip_color_profile',
        'eth' => 'enforce_thumbnail',
        'q' => 'quality',
        'fq' => 'format_quality',
        'mb' => 'max_bytes',
        'f' => 'format',
        'skp' => 'skip_processing',
        'raw' => 'raw',
        'cb' => 'cachebuster',
        'exp' => 'expires',
        'fn' => 'filename',
        'att' => 'return_attachment',
        'pr' => 'preset',
        'msr' => 'max_src_resolution',
        'msfs' => 'max_src_file_size',
        'maf' => 'max_animation_frames',
        'mafr' => 'max_animation_frame_resolution',
        'mrd' => 'max_result_dimension',
    ];

    public function __construct()
    {
        $this->endpoint = config('imgproxy.endpoint', 'http://localhost:8080');
        $this->key = $this->validateHexString(config('imgproxy.key', ''), 'key');
        $this->salt = $this->validateHexString(config('imgproxy.salt', ''), 'salt');
        $this->source_url_mode = SourceUrlMode::fromString(config('imgproxy.default_source_url_mode')) ?? SourceUrlMode::getDefault();
        $this->default_output_extension = OutputExtension::fromExtension(config('imgproxy.default_output_extension')) ?? OutputExtension::getDefault();
        $this->use_short_options = config('imgproxy.use_short_options', false);
        $this->fallback_url = config('imgproxy.fallback_url');
    }

    /**
     * Set the source URL for the image.
     *
     * @param  string  $source_url  The URL of the source image
     */
    public function url(string $source_url): self
    {
        $this->source_url = $source_url;

        return $this;
    }

    /**
     * Set the width of the output image.
     *
     * @param  int  $width  The desired width in pixels
     */
    public function setWidth(int $width): self
    {
        $this->options['width'] = $width;

        return $this;
    }

    /**
     * Set the width of the output image.
     *
     * Alias of {@see setWidth()}.
     *
     * @param  int  $width  The desired width in pixels
     */
    public function width(int $width): self
    {
        return $this->setWidth($width);
    }

    /**
     * Set the height of the output image.
     *
     * @param  int  $height  The desired height in pixels
     */
    public function setHeight(int $height): self
    {
        $this->options['height'] = $height;

        return $this;
    }

    /**
     * Set the height of the output image.
     *
     * Alias of {@see setHeight()}.
     *
     * @param  int  $height  The desired height in pixels
     */
    public function height(int $height): self
    {
        return $this->setHeight($height);
    }

    /**
     * Set the resize mode for the image.
     *
     * @param  ResizeType  $mode  The resize mode ('fit', 'fill', 'crop', 'force')
     *
     * @see \Imsus\ImgProxy\Enums\ResizeType
     */
    public function setResizeType(ResizeType $mode): self
    {
        $this->options['resizing_type'] = $mode->value;

        return $this;
    }

    /**
     * Set resize type to cover (FILL).
     *
     * Resizes the image to fill the specified dimensions, cropping if necessary.
     *
     * Alias of {@see setResizeType()} with {@see ResizeType::FILL}.
     */
    public function cover(): self
    {
        return $this->setResizeType(ResizeType::FILL);
    }

    /**
     * Set resize type to contain (FIT).
     *
     * Resizes the image to fit within the specified dimensions.
     *
     * Alias of {@see setResizeType()} with {@see ResizeType::FIT}.
     */
    public function contain(): self
    {
        return $this->setResizeType(ResizeType::FIT);
    }

    /**
     * Set resize type to fill-down.
     *
     * Resizes the image to fill the specified dimensions, downscaling only.
     * Unlike force(), this maintains aspect ratio.
     *
     * Alias of {@see setResizeType()} with {@see ResizeType::FILL_DOWN}.
     */
    public function fillDown(): self
    {
        return $this->setResizeType(ResizeType::FILL_DOWN);
    }

    /**
     * Set resize type to force (stretch).
     *
     * Resizes the image without keeping the aspect ratio.
     * Similar to CSS object-fit: fill.
     *
     * Alias of {@see setResizeType()} with {@see ResizeType::FORCE}.
     */
    public function force(): self
    {
        return $this->setResizeType(ResizeType::FORCE);
    }

    /**
     * Set the gravity for image positioning (crop, fill, etc.).
     *
     * @param  Gravity  $gravity  The gravity position
     *
     * @see \Imsus\ImgProxy\Enums\Gravity
     */
    public function setGravity(Gravity $gravity): self
    {
        $this->options['gravity'] = $gravity->value;

        return $this;
    }

    /**
     * Set the gravity with offset values.
     *
     * @param  Gravity  $gravity  The gravity position
     * @param  float  $xOffset  X offset (absolute >=1 or relative <1)
     * @param  float  $yOffset  Y offset (absolute >=1 or relative <1)
     */
    public function setGravityWithOffset(Gravity $gravity, float $xOffset, float $yOffset): self
    {
        $this->options['gravity'] = "{$gravity->value}:{$xOffset}:{$yOffset}";

        return $this;
    }

    /**
     * Set the focus point for the image.
     *
     * @param  float  $x  X coordinate (0-1, where 0=left, 1=right)
     * @param  float  $y  Y coordinate (0-1, where 0=top, 1=bottom)
     */
    public function setFocusPoint(float $x, float $y): self
    {
        $this->options['gravity'] = "fp:{$x}:{$y}";

        return $this;
    }

    /**
     * Set the crop dimensions.
     *
     * @param  float  $width  Crop width (absolute >=1, relative <1, or 0 for full)
     * @param  float  $height  Crop height (absolute >=1, relative <1, or 0 for full)
     * @param  Gravity|null  $gravity  Optional gravity position (defaults to center)
     */
    public function crop(float $width, float $height, ?Gravity $gravity = null): self
    {
        $gravity = $gravity ?? Gravity::CENTER;
        $this->options['crop'] = "{$width}:{$height}:{$gravity->value}";

        return $this;
    }

    /**
     * Set the device pixel ratio (DPR) for the image.
     *
     * @param  int  $dpr  The device pixel ratio (1-8)
     *
     * @throws \InvalidArgumentException If DPR is not between 1 and 8
     */
    public function setDpr(int $dpr): self
    {
        if ($dpr < 1 || $dpr > 8) {
            throw new \InvalidArgumentException('DPR (Device Pixel Ratio) must be between 1 and 8');
        }

        $this->options['dpr'] = $dpr;

        return $this;
    }

    /**
     * Set the device pixel ratio (DPR) for the image.
     *
     * Alias of {@see setDpr()}.
     *
     * @param  int  $dpr  The device pixel ratio (1-8)
     */
    public function dpr(int $dpr): self
    {
        return $this->setDpr($dpr);
    }

    /**
     * Set the zoom factor(s) for the image.
     *
     * Unlike DPR, zoom doesn't affect gravity offsets, watermark offsets, and paddings.
     *
     * @param  float  $zoom  Zoom factor (must be greater than 0)
     * @param  float|null  $zoomY  Optional Y zoom factor (if different from X)
     *
     * @throws \InvalidArgumentException If zoom is not greater than 0
     */
    public function zoom(float $zoom, ?float $zoomY = null): self
    {
        if ($zoom <= 0) {
            throw new \InvalidArgumentException('Zoom must be greater than 0');
        }

        if ($zoomY !== null && $zoomY <= 0) {
            throw new \InvalidArgumentException('Zoom must be greater than 0');
        }

        $this->options['z'] = $zoomY !== null ? "{$zoom}:{$zoomY}" : (string) $zoom;

        return $this;
    }

    /**
     * Set the source URL mode (encoded or plain).
     *
     * @param  SourceUrlMode  $source_url_mode  The source URL mode
     *
     * @see \Imsus\ImgProxy\Enums\SourceUrlMode
     */
    public function setMode(SourceUrlMode $source_url_mode): self
    {
        $this->source_url_mode = $source_url_mode;

        return $this;
    }

    /**
     * Set the output file extension.
     *
     * @param  OutputExtension  $extension  The desired file extension
     *
     * @see \Imsus\ImgProxy\Enums\OutputExtension
     */
    public function setExtension(OutputExtension $extension): self
    {
        $this->overridden_extension = $extension;

        return $this;
    }

    /**
     * Set output format to WebP.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::WEBP}.
     */
    public function webp(): self
    {
        return $this->setExtension(OutputExtension::WEBP);
    }

    /**
     * Set output format to AVIF.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::AVIF}.
     */
    public function avif(): self
    {
        return $this->setExtension(OutputExtension::AVIF);
    }

    /**
     * Set output format to PNG.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::PNG}.
     */
    public function png(): self
    {
        return $this->setExtension(OutputExtension::PNG);
    }

    /**
     * Set output format to JPEG.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::JPEG}.
     */
    public function jpg(): self
    {
        return $this->setExtension(OutputExtension::JPEG);
    }

    /**
     * Set output format to GIF.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::GIF}.
     */
    public function gif(): self
    {
        return $this->setExtension(OutputExtension::GIF);
    }

    /**
     * Set output format to SVG.
     *
     * Note: SVG output is only supported when the source image is SVG.
     * When source is SVG and SVG output is requested, imgproxy returns the source unchanged.
     *
     * Alias of {@see setExtension()} with {@see OutputExtension::SVG}.
     */
    public function svg(): self
    {
        return $this->setExtension(OutputExtension::SVG);
    }

    /**
     * Set the image quality (0-100).
     *
     * @param  int  $quality  The quality level (0-100)
     *
     * @throws \InvalidArgumentException If quality is not between 0 and 100
     */
    public function setQuality(int $quality): self
    {
        if ($quality < 0 || $quality > 100) {
            throw new \InvalidArgumentException('Quality must be between 0 and 100');
        }

        $this->options['quality'] = $quality;

        return $this;
    }

    /**
     * Set the image quality (0-100).
     *
     * Alias of {@see setQuality()}.
     *
     * @param  int  $quality  The quality level (0-100)
     */
    public function quality(int $quality): self
    {
        return $this->setQuality($quality);
    }

    /**
     * Set quality for specific image formats.
     *
     * @param  string|array<string, int>  $qualities  Format => quality mapping (e.g., ['jpg' => 80, 'webp' => 90]) or 'format:quality' string
     * @param  int|null  $quality  Quality value (used when first arg is format string)
     */
    public function setFormatQuality(string|array $qualities, ?int $quality = null): self
    {
        $parts = [];

        if (is_string($qualities) && $quality !== null) {
            $parts[] = "{$qualities}:{$quality}";
        } elseif (is_string($qualities)) {
            $parts[] = $qualities;
        } else {
            foreach ($qualities as $format => $q) {
                $parts[] = "{$format}:{$q}";
            }
        }

        $this->options['fq'] = implode(':', $parts);

        return $this;
    }

    /**
     * Set maximum output file size in bytes.
     *
     * imgproxy will auto-degrade quality to fit within this size.
     *
     * @param  int  $bytes  Maximum file size in bytes (0 or greater)
     *
     * @throws \InvalidArgumentException If bytes is negative
     */
    public function setMaxBytes(int $bytes): self
    {
        if ($bytes < 0) {
            throw new \InvalidArgumentException('Max bytes must be 0 or greater');
        }

        $this->options['mb'] = $bytes;

        return $this;
    }

    /**
     * Set the blur effect strength.
     *
     * @param  float  $sigma  Blur sigma (0.0 and above)
     *
     * @throws \InvalidArgumentException If sigma is negative
     */
    public function setBlur(float $sigma): self
    {
        if ($sigma < 0) {
            throw new \InvalidArgumentException('Blur sigma must be 0.0 or greater');
        }

        $this->options['blur'] = $sigma;

        return $this;
    }

    /**
     * Set the sharpen effect strength.
     *
     * @param  float  $sigma  Sharpen sigma (0.0 and above)
     *
     * @throws \InvalidArgumentException If sigma is negative
     */
    public function setSharpen(float $sigma): self
    {
        if ($sigma < 0) {
            throw new \InvalidArgumentException('Sharpen sigma must be 0.0 or greater');
        }

        $this->options['sharpen'] = $sigma;

        return $this;
    }

    /**
     * Enable or disable enlarging the image if it is smaller than the requested size.
     *
     * @param  bool  $enlarge  Whether to enlarge the image
     */
    public function enlarge(bool $enlarge = true): self
    {
        $this->options['el'] = $enlarge ? 1 : 0;

        return $this;
    }

    /**
     * Enable extending the image if it is smaller than the requested size.
     *
     * @param  bool|Gravity  $extend  Whether to enable extending or the gravity position
     */
    public function extend(bool|Gravity $extend = true): self
    {
        if ($extend === false) {
            $this->options['ex'] = '0';

            return $this;
        }

        $gravity = $extend instanceof Gravity ? $extend : Gravity::CENTER;
        $this->options['ex'] = "1:{$gravity->value}:0:0";

        return $this;
    }

    /**
     * Enable extending the image to the requested aspect ratio.
     *
     * @param  Gravity|null  $gravity  Gravity position for extending (defaults to center)
     */
    public function extendAspectRatio(?Gravity $gravity = null): self
    {
        $gravity = $gravity ?? Gravity::CENTER;
        $this->options['exar'] = "1:{$gravity->value}:0:0";

        return $this;
    }

    /**
     * Enable or disable extending the image to the requested aspect ratio.
     *
     * @param  bool  $extend  Whether to enable extending
     */
    public function setExtendAspectRatio(bool $extend): self
    {
        $this->options['exar'] = $extend ? '1:ce:0:0' : '0';

        return $this;
    }

    /**
     * Set the minimum width of the resulting image.
     *
     * @param  int  $width  Minimum width in pixels
     */
    public function minWidth(int $width): self
    {
        $this->options['mw'] = $width;

        return $this;
    }

    /**
     * Set the minimum height of the resulting image.
     *
     * @param  int  $height  Minimum height in pixels
     */
    public function minHeight(int $height): self
    {
        $this->options['mh'] = $height;

        return $this;
    }

    /**
     * Add padding to the image.
     *
     * @param  int  $padding  Padding size in pixels (0 or greater)
     *
     * @throws \InvalidArgumentException If padding is negative
     */
    public function padding(int $padding): self
    {
        if ($padding < 0) {
            throw new \InvalidArgumentException('Padding must be 0 or greater');
        }

        $this->options['pd'] = $padding;

        return $this;
    }

    /**
     * Add padding to the image with CSS-style syntax.
     *
     * @param  int  $top  Top padding (and for all other sides if they haven't been explicitly set)
     * @param  int|null  $right  Right padding (and left if not set)
     * @param  int|null  $bottom  Bottom padding
     * @param  int|null  $left  Left padding
     *
     * @throws \InvalidArgumentException If any padding is negative
     */
    public function paddingAll(int $top, ?int $right = null, ?int $bottom = null, ?int $left = null): self
    {
        if ($top < 0) {
            throw new \InvalidArgumentException('Padding must be 0 or greater');
        }

        $right = $right ?? $top;
        $bottom = $bottom ?? $right;
        $left = $left ?? $bottom;

        if ($right < 0 || $bottom < 0 || $left < 0) {
            throw new \InvalidArgumentException('Padding must be 0 or greater');
        }

        $this->options['pd'] = $top === $right && $right === $bottom && $bottom === $left
            ? $top
            : "{$top}:{$right}:{$bottom}:{$left}";

        return $this;
    }

    /**
     * Set the background color.
     *
     * @param  string  $hex  Hex color without the hash (e.g., 'FF5733')
     *
     * @throws \InvalidArgumentException If hex is not valid 6 digits
     */
    public function background(string $hex): self
    {
        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            throw new \InvalidArgumentException('Background must be a valid 6-digit hex color');
        }

        $this->options['bg'] = $hex;

        return $this;
    }

    /**
     * Enable or disable auto-rotation based on EXIF data.
     *
     * @param  bool  $autoRotate  Whether to auto-rotate the image
     */
    public function autoRotate(bool $autoRotate = true): self
    {
        $this->options['ar'] = $autoRotate ? 1 : 0;

        return $this;
    }

    /**
     * Rotate the image by the specified degrees.
     *
     * @param  Rotation  $rotation  Rotation angle (0, 90, 180, or 270 degrees)
     */
    public function rotate(Rotation $rotation): self
    {
        $this->options['rot'] = $rotation->value();

        return $this;
    }

    /**
     * Enable or disable stripping metadata from the image.
     *
     * @param  bool  $strip  Whether to strip metadata
     */
    public function stripMetadata(bool $strip = true): self
    {
        $this->options['sm'] = $strip ? 1 : 0;

        return $this;
    }

    /**
     * Preserve copyright info while stripping metadata.
     *
     * @param  bool  $keep  Whether to keep copyright info
     */
    public function keepCopyright(bool $keep = true): self
    {
        $this->options['kcr'] = $keep ? 1 : 0;

        return $this;
    }

    /**
     * Transform embedded color profile to sRGB and remove it.
     *
     * @param  bool  $strip  Whether to strip color profile
     */
    public function stripColorProfile(bool $strip = true): self
    {
        $this->options['scp'] = $strip ? 1 : 0;

        return $this;
    }

    /**
     * Use embedded thumbnail for HEIC/AVIF instead of main image.
     *
     * @param  bool  $enforce  Whether to enforce thumbnail
     */
    public function enforceThumbnail(bool $enforce = true): self
    {
        $this->options['eth'] = $enforce ? 1 : 0;

        return $this;
    }

    /**
     * Trim borders from the image.
     *
     * @param  int  $threshold  Trim threshold (0 or greater)
     *
     * @throws \InvalidArgumentException If threshold is negative
     */
    public function trim(int $threshold = 10): self
    {
        if ($threshold < 0) {
            throw new \InvalidArgumentException('Trim threshold must be 0 or greater');
        }

        $this->options['trim'] = $threshold;

        return $this;
    }

    /**
     * Trim borders from the image with advanced options.
     *
     * @param  int  $threshold  Trim threshold (0 or greater)
     * @param  string|null  $color  Hex color to trim (e.g., 'FF5733')
     * @param  bool  $equalHor  Trim equal parts from left and right
     * @param  bool  $equalVer  Trim equal parts from top and bottom
     *
     * @throws \InvalidArgumentException If threshold is negative
     * @throws \InvalidArgumentException If color is not valid 6 digits
     */
    public function trimWithColor(int $threshold = 10, ?string $color = null, bool $equalHor = false, bool $equalVer = false): self
    {
        if ($threshold < 0) {
            throw new \InvalidArgumentException('Trim threshold must be 0 or greater');
        }

        if ($color !== null && ! preg_match('/^[0-9a-fA-F]{6}$/', $color)) {
            throw new \InvalidArgumentException('Trim color must be a valid 6-digit hex color');
        }

        $colorPart = $color ?? '';
        $equalHorPart = $equalHor ? '1' : '0';
        $equalVerPart = $equalVer ? '1' : '0';

        $this->options['trim'] = "{$threshold}:{$colorPart}:{$equalHorPart}:{$equalVerPart}";

        return $this;
    }

    /**
     * Pixelate the image.
     *
     * @param  int  $size  Pixel size (0 or greater)
     *
     * @throws \InvalidArgumentException If size is negative
     */
    public function pixelate(int $size): self
    {
        if ($size < 0) {
            throw new \InvalidArgumentException('Pixel size must be 0 or greater');
        }

        $this->options['pix'] = $size;

        return $this;
    }

    /**
     * Add a watermark image.
     *
     * Format: wm:opacity:position:x_offset:y_offset:scale
     *
     * @param  float  $opacity  Watermark opacity (0.0 to 1.0), defaults to 0.5
     * @param  Gravity|string  $position  Watermark position (Gravity, 're' for repeat, 'ch' for chessboard), defaults to center
     * @param  float  $xOffset  X offset (absolute >=1 or relative <1)
     * @param  float  $yOffset  Y offset (absolute >=1 or relative <1)
     * @param  float  $scale  Scale factor relative to result image, defaults to 0 (no scaling)
     *
     * @throws \InvalidArgumentException If opacity or scale is out of range
     */
    public function watermark(
        float $opacity = 0.5,
        Gravity|string $position = Gravity::CENTER,
        float $xOffset = 0,
        float $yOffset = 0,
        float $scale = 0
    ): self {
        if ($opacity < 0 || $opacity > 1) {
            throw new \InvalidArgumentException('Watermark opacity must be between 0.0 and 1.0');
        }

        if ($scale < 0) {
            throw new \InvalidArgumentException('Watermark scale must be 0 or greater');
        }

        $positionValue = $position instanceof Gravity ? $position->value : $position;
        // Map 'repeat' to 're' and 'chessboard' to 'ch'
        $positionValue = match ($positionValue) {
            'repeat' => 're',
            'chessboard' => 'ch',
            default => $positionValue,
        };
        $this->options['wm'] = "{$opacity}:{$positionValue}:{$xOffset}:{$yOffset}:{$scale}";

        return $this;
    }

    /**
     * Skip processing when output format matches source format.
     *
     * @param  string|string[]  $formats  Format(s) to skip processing for
     */
    public function skipProcessing(string|array $formats): self
    {
        $formats = is_array($formats) ? $formats : [$formats];
        $this->options['skp'] = implode(':', $formats);

        return $this;
    }

    /**
     * Stream unprocessed source image directly.
     *
     * Bypasses all processing, checking, and worker limits.
     *
     * @param  bool  $raw  Whether to enable raw mode
     */
    public function raw(bool $raw = true): self
    {
        $this->options['raw'] = $raw ? 1 : 0;

        return $this;
    }

    /**
     * Add cache buster to bypass CDN/proxy/browser cache.
     *
     * @param  string  $value  Cache buster value
     */
    public function cachebuster(string $value): self
    {
        $this->options['cb'] = $value;

        return $this;
    }

    /**
     * Add cache busting version to force CDN/proxy refresh.
     *
     * Alias of {@see cachebuster()}.
     *
     * @param  int|string  $version  Version identifier (e.g., timestamp, build number, or string)
     */
    public function v(int|string $version): self
    {
        return $this->cachebuster((string) $version);
    }

    /**
     * Set a fallback URL to use when the source URL is invalid.
     *
     * @param  string  $url  The fallback image URL
     */
    public function fallback(string $url): self
    {
        $this->fallback_url = $url;

        return $this;
    }

    /**
     * Set expiration timestamp.
     *
     * imgproxy will return 404 when expired.
     *
     * @param  int  $timestamp  Unix timestamp
     */
    public function expires(int $timestamp): self
    {
        $this->options['exp'] = $timestamp;

        return $this;
    }

    /**
     * Set filename for Content-Disposition header.
     *
     * @param  string  $filename  Filename to use
     * @param  bool  $encoded  Whether the filename is URL-safe Base64 encoded
     */
    public function filename(string $filename, bool $encoded = false): self
    {
        $encodedFilename = $encoded ? rtrim(strtr(base64_encode($filename), '+/', '-_'), '=') : $filename;
        $this->options['fn'] = $encoded ? "{$encodedFilename}:1" : $encodedFilename;

        return $this;
    }

    /**
     * Force download as attachment.
     *
     * @param  bool  $attachment  Whether to force attachment
     */
    public function returnAttachment(bool $attachment = true): self
    {
        $this->options['att'] = $attachment ? 1 : 0;

        return $this;
    }

    /**
     * Use server-defined presets.
     *
     * @param  string|string[]  $presets  Preset name(s) to use
     */
    public function preset(string|array $presets): self
    {
        $presets = is_array($presets) ? $presets : [$presets];
        $this->options['pr'] = implode(':', $presets);

        return $this;
    }

    /**
     * Set maximum source resolution in megapixels.
     *
     * Requires IMGPROXY_ALLOW_SECURITY_OPTIONS=true.
     *
     * @param  float  $resolution  Maximum resolution in megapixels
     */
    public function setMaxSrcResolution(float $resolution): self
    {
        $this->options['msr'] = $resolution;

        return $this;
    }

    /**
     * Set maximum source file size in bytes.
     *
     * Requires IMGPROXY_ALLOW_SECURITY_OPTIONS=true.
     *
     * @param  int  $size  Maximum file size in bytes
     */
    public function setMaxSrcFileSize(int $size): self
    {
        $this->options['msfs'] = $size;

        return $this;
    }

    /**
     * Set maximum animation frames to process.
     *
     * Requires IMGPROXY_ALLOW_SECURITY_OPTIONS=true.
     *
     * @param  int  $frames  Maximum number of frames
     */
    public function setMaxAnimationFrames(int $frames): self
    {
        $this->options['maf'] = $frames;

        return $this;
    }

    /**
     * Set maximum animation frame resolution in megapixels.
     *
     * Requires IMGPROXY_ALLOW_SECURITY_OPTIONS=true.
     *
     * @param  float  $resolution  Maximum resolution in megapixels
     */
    public function setMaxAnimationFrameResolution(float $resolution): self
    {
        $this->options['mafr'] = $resolution;

        return $this;
    }

    /**
     * Set maximum result dimension in pixels.
     *
     * Requires IMGPROXY_ALLOW_SECURITY_OPTIONS=true.
     *
     * @param  int  $dimension  Maximum dimension in pixels
     */
    public function setMaxResultDimension(int $dimension): self
    {
        $this->options['mrd'] = $dimension;

        return $this;
    }

    /**
     * Set the processing string.
     *
     * @param  string  $processing_options  The processing string to be used
     *
     * @see https://docs.imgproxy.net/usage/processing#processing-options
     */
    public function setProcessing(string $processing_options): self
    {
        $this->processing_options = $processing_options;

        return $this;
    }

    /**
     * Build the final ImgProxy URL.
     *
     * @return string The generated ImgProxy URL
     *
     * @throws \InvalidArgumentException If the source URL is invalid
     */
    public function build(): string
    {
        try {
            $this->validateSourceUrl();
        } catch (\InvalidArgumentException $e) {
            if ($this->fallback_url) {
                return $this->fallback_url;
            }

            return $this->source_url;
        }

        $path = $this->buildPath();

        if ($this->key && $this->salt) {
            $signature = $this->generateSignature($path);
        } else {
            $signature = 'insecure';
        }

        return "{$this->endpoint}/{$signature}/{$path}";
    }

    /**
     * Create a copy of the current builder with all options preserved.
     *
     * @return self A new ImgProxy instance with the same options
     */
    public function copy(): self
    {
        $copy = new self;
        $copy->endpoint = $this->endpoint;
        $copy->key = $this->key;
        $copy->salt = $this->salt;
        $copy->source_url = $this->source_url;
        $copy->source_url_mode = $this->source_url_mode;
        $copy->default_output_extension = $this->default_output_extension;
        $copy->overridden_extension = $this->overridden_extension;
        $copy->options = $this->options;
        $copy->processing_options = $this->processing_options;
        $copy->use_short_options = $this->use_short_options;
        $copy->fallback_url = $this->fallback_url;

        return $copy;
    }

    private function validateSourceUrl(): void
    {
        if (empty($this->source_url)) {
            throw new InvalidArgumentException('Invalid source URL');
        }

        $validSchemes = ['s3://', 'local://', 'gs://', 'abs://', 'swift://'];
        $hasValidScheme = false;

        foreach ($validSchemes as $scheme) {
            if (str_starts_with($this->source_url, $scheme)) {
                $hasValidScheme = true;
                break;
            }
        }

        if (! $hasValidScheme && ! filter_var($this->source_url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid source URL');
        }
    }

    private function buildPath(): string
    {
        $processing_options = $this->processing_options ?? $this->buildProcessingOptions();
        $extension = $this->overridden_extension ?? OutputExtension::fromExtension(pathinfo($this->source_url, PATHINFO_EXTENSION)) ?? $this->default_output_extension;

        if ($this->source_url_mode === SourceUrlMode::PLAIN) {
            $path = "{$processing_options}/plain/{$this->source_url}";
            $path .= "@{$extension->value}";
        } else {
            $encoded_source_url = rtrim(strtr(base64_encode($this->source_url), '+/', '-_'), '=');
            $path = "{$processing_options}/{$encoded_source_url}";
            $path .= ".{$extension->value}";
        }

        return $path;
    }

    private function buildProcessingOptions(): string
    {
        $map = [];

        foreach ($this->options as $key => $value) {
            if ($this->use_short_options) {
                $shortKey = self::SHORT_OPTIONS[$key] ?? $key;
                $map[] = "{$shortKey}:{$value}";
            } else {
                // Convert short keys to full names when short options are disabled
                $fullKey = self::FULL_OPTIONS[$key] ?? $key;
                $map[] = "{$fullKey}:{$value}";
            }
        }

        return implode('/', $map);
    }

    /**
     * Generates a signature for the given path.
     *
     * @param  string  $path  The path to generate a signature for
     * @return string The generated signature
     */
    public function generateSignature(string $path): string
    {
        $data = "{$this->salt}/{$path}";
        $hmac = hash_hmac('sha256', $data, $this->key, true);
        $signature = base64_encode($hmac);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $signature);

        return $signature;
    }

    private function validateHexString(string $value, string $name): string
    {
        if ($value === '') {
            return $value;
        }

        if (! ctype_xdigit($value)) {
            throw new \InvalidArgumentException("The {$name} must be a hex-encoded string.");
        }

        return pack('H*', $value);
    }
}
