<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use BackedEnum;
use InvalidArgumentException;
use LaravelImgproxy\LaravelImgproxy\Enums\Format;
use LaravelImgproxy\LaravelImgproxy\Enums\Gravity;
use LaravelImgproxy\LaravelImgproxy\Enums\ResizeType;
use LaravelImgproxy\LaravelImgproxy\Enums\WatermarkPosition;
use ValueError;

/**
 * Immutable fluent builder for imgproxy URLs.
 *
 * Every mutation returns a new instance, so a base builder can be reused
 * for several URL variants. The source and the base URL are fixed at
 * construction; processing option segments are appended in call order.
 *
 * Option segment formats and validation ranges target imgproxy v4 (4.0.x).
 * They are verified against the v4 processing docs
 * (https://docs.imgproxy.net/usage/processing), the v4 server source at
 * imgproxy master eef3b31 (2026-08-06), and a live v4 imgproxy
 * (docker.io/imgproxy: v4, 2026-08-09). Re-verify the option methods and
 * the Enums namespace when the imgproxy server version changes.
 */
final class Builder
{
    /** @var list<string> */
    private const array ENCODINGS = ['base64', 'plain'];

    private readonly UrlSigner $signer;

    /**
     * @param  list<string>  $segments  Processing option segments, in call order.
     * @param  string|null  $key  Hex-encoded signing key, or null for unsigned URLs.
     * @param  string|null  $salt  Hex-encoded signing salt, or null for unsigned URLs.
     * @param  int|null  $signatureSize  Signature bytes to keep (1-32), or null for the full 32.
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $source,
        private readonly string $encoding = 'base64',
        private readonly array $segments = [],
        ?string $key = null,
        ?string $salt = null,
        ?int $signatureSize = null,
    ) {
        $this->validateEncoding($encoding);
        $this->signer = new UrlSigner($key, $salt, $signatureSize);
    }

    /**
     * The full imgproxy URL.
     */
    public function url(): string
    {
        $path = '/'.($this->segments === [] ? '' : implode('/', $this->segments).'/').$this->encodedSource();

        $signature = $this->signer->sign($path) ?: 'unsafe';

        return rtrim($this->baseUrl, '/').'/'.$signature.$path;
    }

    /**
     * The full imgproxy URL.
     */
    public function __toString(): string
    {
        return $this->url();
    }

    /**
     * Return a copy of the builder with a different source encoding.
     *
     * @throws InvalidArgumentException When the encoding is not supported.
     */
    public function encoding(string $encoding): self
    {
        return new self(
            $this->baseUrl,
            $this->source,
            $encoding,
            $this->segments,
            $this->signer->key(),
            $this->signer->salt(),
            $this->signer->signatureSize(),
        );
    }

    /**
     * Resize the image to the given size.
     *
     * The `enlarge` and `extend` flags are only emitted when they differ
     * from imgproxy's default `false`.
     *
     * @throws InvalidArgumentException When the resize type is unknown or a size is negative.
     */
    public function resize(
        ResizeType|string $type,
        ?int $width = null,
        ?int $height = null,
        bool $enlarge = false,
        bool $extend = false,
    ): self {
        $type = self::resolveEnum($type, ResizeType::class, 'resize type');

        if ($width !== null) {
            self::requireNonNegative('resize width', $width);
        }

        if ($height !== null) {
            self::requireNonNegative('resize height', $height);
        }

        $args = [$type->value];

        if ($width !== null) {
            $args[] = (string) $width;
        }

        if ($height !== null) {
            $args[] = (string) $height;
        }

        if ($enlarge || $extend) {
            $args[] = $enlarge ? '1' : '0';
        }

        if ($extend) {
            $args[] = '1';
        }

        return $this->withSegment('rs:'.implode(':', $args));
    }

    /**
     * Set the width, height, enlarge, and extend flags in one option.
     *
     * The `enlarge` and `extend` flags are only emitted when they differ
     * from imgproxy's default `false`; the gravity is emitted only when set.
     *
     * @throws InvalidArgumentException When a size is negative or the gravity is unknown or smart.
     */
    public function size(
        ?int $width = null,
        ?int $height = null,
        bool $enlarge = false,
        bool $extend = false,
        Gravity|string|null $gravity = null,
    ): self {
        if ($width !== null) {
            self::requireNonNegative('size width', $width);
        }

        if ($height !== null) {
            self::requireNonNegative('size height', $height);
        }

        $args = [];

        if ($width !== null) {
            $args[] = (string) $width;
        }

        if ($height !== null) {
            $args[] = (string) $height;
        }

        if ($enlarge || $extend || $gravity !== null) {
            $args[] = $enlarge ? '1' : '0';
        }

        if ($extend || $gravity !== null) {
            $args[] = $extend ? '1' : '0';
        }

        if ($gravity !== null) {
            $args[] = self::requireNonSmartGravity($gravity, 'size')->value;
        }

        return $this->withSegment('s:'.implode(':', $args));
    }

    /**
     * Set the resizing type without a size.
     *
     * @throws InvalidArgumentException When the resizing type is unknown.
     */
    public function resizingType(ResizeType|string $type): self
    {
        $type = self::resolveEnum($type, ResizeType::class, 'resizing type');

        return $this->withSegment('rt:'.$type->value);
    }

    /**
     * Set the minimum width of the resulting image.
     *
     * @throws InvalidArgumentException When the min width is negative.
     */
    public function minWidth(int $width): self
    {
        self::requireNonNegative('min width', $width);

        return $this->withSegment('mw:'.$width);
    }

    /**
     * Set the minimum height of the resulting image.
     *
     * @throws InvalidArgumentException When the min height is negative.
     */
    public function minHeight(int $height): self
    {
        self::requireNonNegative('min height', $height);

        return $this->withSegment('mh:'.$height);
    }

    /**
     * Multiply the image dimensions by the given factors.
     *
     * Unlike the `dpr` option, `zoom` does not affect gravity offsets,
     * watermark offsets, or paddings. A single factor applies to both axes.
     *
     * @throws InvalidArgumentException When a factor is not greater than zero.
     */
    public function zoom(int|float $x, int|float|null $y = null): self
    {
        self::requirePositive('zoom factor', $x);

        $args = [(string) $x];

        if ($y !== null) {
            self::requirePositive('zoom factor', $y);
            $args[] = (string) $y;
        }

        return $this->withSegment('z:'.implode(':', $args));
    }

    /**
     * Set the width of the resulting image.
     *
     * A width of `0` makes imgproxy calculate the width from the height and
     * the source aspect ratio.
     *
     * @throws InvalidArgumentException When the width is negative.
     */
    public function width(int $width): self
    {
        self::requireNonNegative('width', $width);

        return $this->withSegment('w:'.$width);
    }

    /**
     * Set the height of the resulting image.
     *
     * A height of `0` makes imgproxy calculate the height from the width and
     * the source aspect ratio.
     *
     * @throws InvalidArgumentException When the height is negative.
     */
    public function height(int $height): self
    {
        self::requireNonNegative('height', $height);

        return $this->withSegment('h:'.$height);
    }

    /**
     * Set the quality of the resulting image, as a percentage.
     *
     * A quality of `0` makes imgproxy fall back to its configured quality.
     *
     * @throws InvalidArgumentException When the quality is outside 0-100.
     */
    public function quality(int $quality): self
    {
        if ($quality < 0 || $quality > 100) {
            throw new InvalidArgumentException("The imgproxy quality must be between 0 and 100, [{$quality}] given.");
        }

        return $this->withSegment('q:'.$quality);
    }

    /**
     * Set the resulting image format.
     *
     * @throws InvalidArgumentException When the format is not supported.
     */
    public function format(Format|string $format): self
    {
        $format = self::resolveEnum($format, Format::class, 'format');

        return $this->withSegment('f:'.$format->value);
    }

    /**
     * Redefine the quality of specific output formats.
     *
     * The map keys are format values (e.g. `'webp'`), the values are
     * qualities between 0 and 100.
     *
     * @param  array<string, int>  $qualities  Format => quality pairs.
     *
     * @throws InvalidArgumentException When a format is unknown or a quality is outside 0-100.
     */
    public function formatQuality(array $qualities): self
    {
        $args = [];

        foreach ($qualities as $format => $quality) {
            $format = self::resolveEnum((string) $format, Format::class, 'format quality format');

            if ($quality < 0 || $quality > 100) {
                throw new InvalidArgumentException(
                    "The imgproxy format quality must be between 0 and 100, [{$quality}] given.",
                );
            }

            $args[] = $format->value;
            $args[] = (string) $quality;
        }

        return $this->withSegment('fq:'.implode(':', $args));
    }

    /**
     * Skip the processing of the given source formats.
     *
     * @throws InvalidArgumentException When a format is unknown.
     */
    public function skipProcessing(Format|string ...$formats): self
    {
        $args = [];

        foreach ($formats as $format) {
            $format = self::resolveEnum($format, Format::class, 'skip processing format');
            $args[] = $format->value;
        }

        return $this->withSegment('skp:'.implode(':', $args));
    }

    /**
     * Respond with the raw unprocessed source image.
     *
     * This is the imgproxy `raw` processing option; it is named `rawResponse`
     * to avoid colliding with the `raw()` escape hatch.
     */
    public function rawResponse(bool $enabled = true): self
    {
        return $this->withSegment('raw:'.($enabled ? '1' : '0'));
    }

    /**
     * Define an area of the image to be processed (crop before resize).
     *
     * Values below `1` are treated by imgproxy as relative, `0` uses the
     * full source dimension.
     *
     * @throws InvalidArgumentException When a crop size is negative or the gravity is unknown.
     */
    public function crop(int|float $width, int|float $height, Gravity|string|null $gravity = null): self
    {
        self::requireNonNegative('crop width', $width);
        self::requireNonNegative('crop height', $height);

        $args = [(string) $width, (string) $height];

        if ($gravity !== null) {
            $gravity = self::resolveEnum($gravity, Gravity::class, 'gravity');
            $args[] = $gravity->value;
        }

        return $this->withSegment('c:'.implode(':', $args));
    }

    /**
     * Remove the surrounding background of the image.
     *
     * The color is a 3 or 6 digit hex value, with an optional leading `#`.
     * The equal flags only cut equal amounts from both sides; they are
     * emitted only when set.
     *
     * @throws InvalidArgumentException When the threshold is negative or the color is not a hex value.
     */
    public function trim(float $threshold, ?string $color = null, bool $equalHor = false, bool $equalVer = false): self
    {
        self::requireNonNegative('trim threshold', $threshold);

        $args = [(string) $threshold];

        if ($color !== null || $equalHor || $equalVer) {
            $args[] = $color !== null ? self::hexColor($color, 'trim color') : '';
        }

        if ($equalHor || $equalVer) {
            $args[] = $equalHor ? '1' : '0';
        }

        if ($equalVer) {
            $args[] = '1';
        }

        return $this->withSegment('t:'.implode(':', $args));
    }

    /**
     * Add padding around the processed image using CSS-style syntax.
     *
     * Omitted sides default as in CSS: `right` follows `top`, `bottom`
     * follows `top`, and `left` follows `right`.
     *
     * @throws InvalidArgumentException When a padding side is negative.
     */
    public function padding(int $top, ?int $right = null, ?int $bottom = null, ?int $left = null): self
    {
        self::requireNonNegative('padding top', $top);

        $args = [(string) $top];

        if ($right !== null) {
            self::requireNonNegative('padding right', $right);
            $args[] = (string) $right;
        }

        if ($bottom !== null) {
            self::requireNonNegative('padding bottom', $bottom);
            $args[] = (string) $bottom;
        }

        if ($left !== null) {
            self::requireNonNegative('padding left', $left);
            $args[] = (string) $left;
        }

        return $this->withSegment('pd:'.implode(':', $args));
    }

    /**
     * Set the gravity used when imgproxy cuts parts of the image.
     *
     * Offsets are emitted only when non-zero; values below `1` are treated
     * by imgproxy as relative.
     *
     * @throws InvalidArgumentException When the gravity is unknown.
     */
    public function gravity(Gravity|string $gravity, int|float $xOffset = 0, int|float $yOffset = 0): self
    {
        $gravity = self::resolveEnum($gravity, Gravity::class, 'gravity');

        $args = [$gravity->value];

        if ($xOffset !== 0 || $yOffset !== 0) {
            $args[] = (string) $xOffset;
        }

        if ($yOffset !== 0) {
            $args[] = (string) $yOffset;
        }

        return $this->withSegment('g:'.implode(':', $args));
    }

    /**
     * Set the gravity focus point.
     *
     * The offsets define the coordinates of the center of the resulting
     * image, where `0` and `1` are left/right for `x` and top/bottom for `y`.
     *
     * @throws InvalidArgumentException When an offset is outside 0-1.
     */
    public function focusPoint(float $x, float $y): self
    {
        if ($x < 0 || $x > 1 || $y < 0 || $y > 1) {
            throw new InvalidArgumentException(
                "The imgproxy focus point offsets must be between 0 and 1, [{$x}:{$y}] given.",
            );
        }

        return $this->withSegment("g:fp:{$x}:{$y}");
    }

    /**
     * Multiply the image dimensions by this factor for HiDPI (Retina) displays.
     *
     * @throws InvalidArgumentException When the dpr is not greater than zero.
     */
    public function dpr(int|float $dpr): self
    {
        self::requirePositive('dpr', $dpr);

        return $this->withSegment('dpr:'.$dpr);
    }

    /**
     * Apply a gaussian blur filter with the given mask size.
     *
     * @throws InvalidArgumentException When the sigma is not greater than zero.
     */
    public function blur(int|float $sigma): self
    {
        self::requirePositive('blur sigma', $sigma);

        return $this->withSegment('bl:'.$sigma);
    }

    /**
     * Apply the sharpen filter with the given mask size.
     *
     * As an approximate guideline, use 0.5 sigma for 4 pixels/mm, 1.0 for
     * 12 pixels/mm, and 1.5 for 16 pixels/mm.
     *
     * @throws InvalidArgumentException When the sigma is not greater than zero.
     */
    public function sharpen(int|float $sigma): self
    {
        self::requirePositive('sharpen sigma', $sigma);

        return $this->withSegment('sh:'.$sigma);
    }

    /**
     * Apply the pixelate filter with the given individual pixel size.
     *
     * @throws InvalidArgumentException When the pixel size is negative.
     */
    public function pixelate(int $size): self
    {
        self::requireNonNegative('pixelate size', $size);

        return $this->withSegment('pix:'.$size);
    }

    /**
     * Rotate the image by the given angle.
     *
     * imgproxy only supports angles that are multiples of 90 degrees.
     *
     * @throws InvalidArgumentException When the angle is negative or not a multiple of 90.
     */
    public function rotate(int $angle): self
    {
        if ($angle < 0 || $angle % 90 !== 0) {
            throw new InvalidArgumentException(
                "The imgproxy rotate angle must be a non-negative multiple of 90, [{$angle}] given.",
            );
        }

        return $this->withSegment('rot:'.$angle);
    }

    /**
     * Automatically rotate the image based on the EXIF orientation.
     */
    public function autoRotate(bool $autoRotate): self
    {
        return $this->withSegment('ar:'.($autoRotate ? '1' : '0'));
    }

    /**
     * Flip the image along the horizontal and/or vertical axes.
     */
    public function flip(bool $horizontal = false, bool $vertical = false): self
    {
        return $this->withSegment('fl:'.($horizontal ? '1' : '0').':'.($vertical ? '1' : '0'));
    }

    /**
     * Enlarge the image when it is smaller than the given size.
     */
    public function enlarge(bool $enlarge): self
    {
        return $this->withSegment('el:'.($enlarge ? '1' : '0'));
    }

    /**
     * Extend the image when it is smaller than the given size.
     *
     * imgproxy does not support the smart gravity for extension.
     *
     * @throws InvalidArgumentException When the gravity is unknown or is the smart gravity.
     */
    public function extend(bool $extend, Gravity|string|null $gravity = null): self
    {
        $args = [$extend ? '1' : '0'];

        if ($gravity !== null) {
            $args[] = self::requireNonSmartGravity($gravity, 'extend')->value;
        }

        return $this->withSegment('ex:'.implode(':', $args));
    }

    /**
     * Extend the image to the requested aspect ratio.
     *
     * imgproxy does not support the smart gravity for extension.
     *
     * @throws InvalidArgumentException When the gravity is unknown or is the smart gravity.
     */
    public function extendAspectRatio(bool $extend, Gravity|string|null $gravity = null): self
    {
        $args = [$extend ? '1' : '0'];

        if ($gravity !== null) {
            $args[] = self::requireNonSmartGravity($gravity, 'extend aspect ratio')->value;
        }

        return $this->withSegment('exar:'.implode(':', $args));
    }

    /**
     * Fill the resulting image background with the given color.
     *
     * The color is a 3 or 6 digit hex value, with an optional leading `#`.
     *
     * @throws InvalidArgumentException When the color is not a valid hex value.
     */
    public function background(string $color): self
    {
        return $this->withSegment('bg:'.self::hexColor($color, 'background color'));
    }

    /**
     * Place a watermark on the processed image.
     *
     * The final opacity is the configured base opacity multiplied by
     * `$opacity`. Offsets are emitted only when non-zero; values below `1`
     * and above `-1` are treated by imgproxy as relative. A `$scale` of `0`
     * leaves the watermark size unchanged.
     *
     * @throws InvalidArgumentException When the opacity is outside 0-1, the position is unknown, or the scale is negative.
     */
    public function watermark(
        int|float $opacity,
        WatermarkPosition|string|null $position = null,
        int|float $xOffset = 0,
        int|float $yOffset = 0,
        int|float $scale = 0,
    ): self {
        if ($opacity <= 0 || $opacity > 1) {
            throw new InvalidArgumentException(
                "The imgproxy watermark opacity must be greater than 0 and at most 1, [{$opacity}] given.",
            );
        }

        self::requireNonNegative('watermark scale', $scale);

        if ($position === null && ($xOffset !== 0 || $yOffset !== 0 || $scale !== 0)) {
            $position = WatermarkPosition::Center;
        }

        $args = [(string) $opacity];

        if ($position !== null) {
            $position = self::resolveEnum($position, WatermarkPosition::class, 'watermark position');
            $args[] = $position->value;
        }

        if ($xOffset !== 0 || $yOffset !== 0 || $scale !== 0) {
            $args[] = (string) $xOffset;
        }

        if ($yOffset !== 0 || $scale !== 0) {
            $args[] = (string) $yOffset;
        }

        if ($scale !== 0) {
            $args[] = (string) $scale;
        }

        return $this->withSegment('wm:'.implode(':', $args));
    }

    /**
     * Strip the output image metadata (EXIF, IPTC, etc.).
     */
    public function stripMetadata(bool $strip): self
    {
        return $this->withSegment('sm:'.($strip ? '1' : '0'));
    }

    /**
     * Keep the copyright info while stripping metadata.
     */
    public function keepCopyright(bool $keep): self
    {
        return $this->withSegment('kcr:'.($keep ? '1' : '0'));
    }

    /**
     * Transform the embedded color profile to sRGB and remove it from the image.
     */
    public function stripColorProfile(bool $strip): self
    {
        return $this->withSegment('scp:'.($strip ? '1' : '0'));
    }

    /**
     * Keep high bit images high bit instead of downscaling them to 8 bit.
     */
    public function preserveHdr(bool $enable): self
    {
        return $this->withSegment('ph:'.($enable ? '1' : '0'));
    }

    /**
     * Always use the embedded thumbnail of the source image when available.
     */
    public function enforceThumbnail(bool $enforce): self
    {
        return $this->withSegment('eth:'.($enforce ? '1' : '0'));
    }

    /**
     * Return the processed image as an attachment instead of inline.
     */
    public function returnAttachment(bool $attachment): self
    {
        return $this->withSegment('att:'.($attachment ? '1' : '0'));
    }

    /**
     * Add a cache buster to bypass CDN, proxy, and browser caches.
     *
     * @throws InvalidArgumentException When the cache buster is empty.
     */
    public function cacheBuster(string $buster): self
    {
        if ($buster === '') {
            throw new InvalidArgumentException('The imgproxy cache buster must not be empty.');
        }

        return $this->withSegment('cb:'.$buster);
    }

    /**
     * Set the unix timestamp after which imgproxy returns 404 for the URL.
     *
     * A timestamp of `0` disables the expiration check. imgproxy itself
     * rejects URLs that are already expired at request time.
     *
     * @throws InvalidArgumentException When the timestamp is negative.
     */
    public function expires(int $timestamp): self
    {
        if ($timestamp < 0) {
            throw new InvalidArgumentException(
                "The imgproxy expires timestamp must not be negative, [{$timestamp}] given.",
            );
        }

        return $this->withSegment('exp:'.$timestamp);
    }

    /**
     * Set the filename used in the `Content-Disposition` header.
     *
     * When `$encoded` is `true`, the filename is already URL-safe Base64
     * encoded and the flag tells imgproxy to decode it.
     *
     * @throws InvalidArgumentException When the filename is empty.
     */
    public function filename(string $filename, bool $encoded = false): self
    {
        if ($filename === '') {
            throw new InvalidArgumentException('The imgproxy filename must not be empty.');
        }

        return $this->withSegment('fn:'.$filename.($encoded ? ':1' : ''));
    }

    /**
     * Apply server-side presets configured on the imgproxy instance.
     *
     * This is the imgproxy `preset` processing option, which refers to
     * presets defined on the server; it is named `imgproxyPreset` to avoid
     * colliding with the config-defined preset composition.
     *
     * @throws InvalidArgumentException When a preset name is empty.
     */
    public function imgproxyPreset(string $name, string ...$more): self
    {
        $names = [$name, ...$more];

        foreach ($names as $preset) {
            if ($preset === '') {
                throw new InvalidArgumentException('The imgproxy preset name must not be empty.');
            }
        }

        return $this->withSegment('pr:'.implode(':', $names));
    }

    /**
     * Redefine the maximum source image resolution, in megapixels.
     *
     * The server only accepts this option when security options are allowed.
     *
     * @throws InvalidArgumentException When the resolution is negative.
     */
    public function maxSrcResolution(int|float $megapixels): self
    {
        self::requireNonNegative('max source resolution', $megapixels);

        return $this->withSegment('msr:'.$megapixels);
    }

    /**
     * Redefine the maximum source image file size, in bytes.
     *
     * The server only accepts this option when security options are allowed.
     *
     * @throws InvalidArgumentException When the size is negative.
     */
    public function maxSrcFileSize(int $bytes): self
    {
        self::requireNonNegative('max source file size', $bytes);

        return $this->withSegment('msfs:'.$bytes);
    }

    /**
     * Redefine the maximum number of animation frames.
     *
     * The server only accepts this option when security options are allowed.
     *
     * @throws InvalidArgumentException When the frame count is not greater than zero.
     */
    public function maxAnimationFrames(int $frames): self
    {
        self::requirePositive('max animation frames', $frames);

        return $this->withSegment('maf:'.$frames);
    }

    /**
     * Redefine the maximum animation frame resolution, in megapixels.
     *
     * The server only accepts this option when security options are allowed.
     *
     * @throws InvalidArgumentException When the resolution is negative.
     */
    public function maxAnimationFrameResolution(int|float $megapixels): self
    {
        self::requireNonNegative('max animation frame resolution', $megapixels);

        return $this->withSegment('mafr:'.$megapixels);
    }

    /**
     * Redefine the maximum dimension of the resulting image, in pixels.
     *
     * The server only accepts this option when security options are allowed.
     *
     * @throws InvalidArgumentException When the dimension is negative.
     */
    public function maxResultDimension(int $pixels): self
    {
        self::requireNonNegative('max result dimension', $pixels);

        return $this->withSegment('mrd:'.$pixels);
    }

    /**
     * Append a processing option segment verbatim, without validation.
     *
     * The segment lands at the current position in the option chain, in call
     * order. Use it for imgproxy options that are not yet covered by a typed
     * method.
     */
    public function raw(string $segment): self
    {
        return $this->withSegment($segment);
    }

    /**
     * Return a copy of the builder with the given option segment appended.
     */
    private function withSegment(string $segment): self
    {
        return new self(
            $this->baseUrl,
            $this->source,
            $this->encoding,
            [...$this->segments, $segment],
            $this->signer->key(),
            $this->signer->salt(),
            $this->signer->signatureSize(),
        );
    }

    /**
     * The source encoded as a URL path segment, in the current encoding.
     *
     * The plain encoding percent-encodes the source behind a `plain/` prefix;
     * the default encoding is URL-safe base64 without padding.
     */
    private function encodedSource(): string
    {
        if ($this->encoding === 'plain') {
            return 'plain/'.rawurlencode($this->source);
        }

        return rtrim(strtr(base64_encode($this->source), '+/', '-_'), '=');
    }

    /**
     * Normalize an enum argument given as an enum instance or its backing value.
     *
     * @template T of BackedEnum
     *
     * @param  BackedEnum|string  $value  The enum instance or its backing value.
     * @param  class-string<T>  $enumClass  The backed enum class.
     * @param  string  $label  The option name used in the error message.
     * @return T
     *
     * @throws InvalidArgumentException When the string is not a supported value.
     */
    private static function resolveEnum(BackedEnum|string $value, string $enumClass, string $label): BackedEnum
    {
        if ($value instanceof $enumClass) {
            return $value;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException("The imgproxy {$label} must be a {$enumClass} or one of its values.");
        }

        try {
            return $enumClass::from($value);
        } catch (ValueError) {
            $supported = implode(', ', array_map(
                static fn (BackedEnum $case): string => (string) $case->value,
                $enumClass::cases(),
            ));

            throw new InvalidArgumentException(
                "Unknown imgproxy {$label} [{$value}]. Supported values: {$supported}.",
            );
        }
    }

    /**
     * Resolve an extend gravity and reject the smart gravity.
     *
     * imgproxy does not support the smart gravity for extension.
     *
     * @throws InvalidArgumentException When the gravity is unknown or is the smart gravity.
     */
    private static function requireNonSmartGravity(Gravity|string $gravity, string $option): Gravity
    {
        $gravity = self::resolveEnum($gravity, Gravity::class, "{$option} gravity");

        if ($gravity === Gravity::Smart) {
            throw new InvalidArgumentException("The imgproxy {$option} does not support the smart gravity.");
        }

        return $gravity;
    }

    /**
     * Validate a 3 or 6 digit hex color and strip an optional leading `#`.
     *
     * @throws InvalidArgumentException When the color is not a valid hex value.
     */
    private static function hexColor(string $color, string $option): string
    {
        $hex = str_starts_with($color, '#') ? substr($color, 1) : $color;

        if (preg_match('/\A[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?\z/', $hex) !== 1) {
            throw new InvalidArgumentException(
                "The imgproxy {$option} must be a 3 or 6 digit hex value, [{$color}] given.",
            );
        }

        return $hex;
    }

    /**
     * @throws InvalidArgumentException When the value is negative.
     */
    private static function requireNonNegative(string $option, int|float $value): void
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The imgproxy {$option} must not be negative, [{$value}] given.");
        }
    }

    /**
     * @throws InvalidArgumentException When the value is not greater than zero.
     */
    private static function requirePositive(string $option, int|float $value): void
    {
        if ($value <= 0) {
            throw new InvalidArgumentException("The imgproxy {$option} must be greater than zero, [{$value}] given.");
        }
    }

    /**
     * @throws InvalidArgumentException When the encoding is not supported.
     */
    private function validateEncoding(string $encoding): void
    {
        if (! in_array($encoding, self::ENCODINGS, true)) {
            throw new InvalidArgumentException("Unsupported imgproxy source encoding [{$encoding}].");
        }
    }
}
