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
 * (https://docs.imgproxy.net/usage/processing) and the v4 server source at
 * imgproxy master eef3b31 (2026-08-06). Re-verify the option methods and
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
     * Enlarge the image when it is smaller than the given size.
     */
    public function enlarge(bool $enlarge): self
    {
        return $this->withSegment('en:'.($enlarge ? '1' : '0'));
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
            $gravity = self::resolveEnum($gravity, Gravity::class, 'gravity');

            if ($gravity === Gravity::Smart) {
                throw new InvalidArgumentException('The imgproxy extend option does not support the smart gravity.');
            }

            $args[] = $gravity->value;
        }

        return $this->withSegment('ex:'.implode(':', $args));
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
        $hex = str_starts_with($color, '#') ? substr($color, 1) : $color;

        if (preg_match('/\A[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?\z/', $hex) !== 1) {
            throw new InvalidArgumentException(
                "The imgproxy background color must be a 3 or 6 digit hex value, [{$color}] given.",
            );
        }

        return $this->withSegment('bg:'.$hex);
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
