<?php

namespace Imsus\ImgProxy;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;
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

    /** @var array<string, string | int> */
    private array $options = [];

    private ?string $processing_options = null;

    public function __construct()
    {
        $this->endpoint = config('imgproxy.endpoint', 'http://localhost:8080');
        $this->key = $this->validateHexString(config('imgproxy.key', ''), 'key');
        $this->salt = $this->validateHexString(config('imgproxy.salt', ''), 'salt');
        $this->source_url_mode = SourceUrlMode::fromString(config('imgproxy.default_source_url_mode')) ?? SourceUrlMode::getDefault();
        $this->default_output_extension = OutputExtension::fromExtension(config('imgproxy.default_output_extension')) ?? OutputExtension::getDefault();
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
     * Set the crop dimensions.
     *
     * @param  int  $width  Crop width
     * @param  int  $height  Crop height
     * @param  Gravity|null  $gravity  Optional gravity position (defaults to center)
     */
    public function crop(int $width, int $height, ?Gravity $gravity = null): self
    {
        $gravity = $gravity ?? Gravity::CENTER;
        $this->options['crop'] = "{$width}:{$height}:{$gravity->value}";

        return $this;
    }

    /**
     * Set the device pixel ratio (DPR) for the image.
     *
     * @param  int  $dpr  The device pixel ratio (1-8)
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
     * Set the image quality (0-100).
     *
     * @param  int  $quality  The quality level (0-100)
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
     * Set the blur effect strength.
     *
     * @param  float  $sigma  Blur sigma (0.0 and above)
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
     * Set the brightness adjustment (-255 to 255).
     *
     * @param  int  $brightness  Brightness adjustment (-255 to 255)
     */
    public function setBrightness(int $brightness): self
    {
        if ($brightness < -255 || $brightness > 255) {
            throw new \InvalidArgumentException('Brightness must be between -255 and 255');
        }

        $this->options['brightness'] = $brightness;

        return $this;
    }

    /**
     * Set the contrast adjustment (0.0 and above).
     *
     * @param  float  $contrast  Contrast multiplier (0.0 and above)
     */
    public function setContrast(float $contrast): self
    {
        if ($contrast < 0) {
            throw new \InvalidArgumentException('Contrast must be 0.0 or greater');
        }

        $this->options['contrast'] = $contrast;

        return $this;
    }

    /**
     * Set the saturation adjustment (0.0 and above).
     *
     * @param  float  $saturation  Saturation multiplier (0.0 and above)
     */
    public function setSaturation(float $saturation): self
    {
        if ($saturation < 0) {
            throw new \InvalidArgumentException('Saturation must be 0.0 or greater');
        }

        $this->options['saturation'] = $saturation;

        return $this;
    }

    /**
     * Add padding to the image.
     *
     * @param  int  $padding  Padding size in pixels (0 or greater)
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
     * Set the background color.
     *
     * @param  string  $hex  Hex color without the hash (e.g., 'FF5733')
     */
    public function background(string $hex): self
    {
        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            throw new \InvalidArgumentException('Background must be a valid 6-digit hex color');
        }

        $this->options['bg'] = "#{$hex}";

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
     * @param  int  $degrees  Rotation angle in degrees (0-360)
     */
    public function rotate(int $degrees): self
    {
        if ($degrees < 0 || $degrees > 360) {
            throw new \InvalidArgumentException('Rotation must be between 0 and 360 degrees');
        }

        $this->options['rot'] = $degrees;

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
     * Trim borders from the image.
     *
     * @param  int  $threshold  Trim threshold (0 or greater)
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
     * Pixelate the image.
     *
     * @param  int  $size  Pixel size (0 or greater)
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
     * @param  Gravity|null  $position  Watermark position, defaults to center
     * @param  int  $xOffset  X offset in pixels
     * @param  int  $yOffset  Y offset in pixels
     * @param  float  $scale  Scale factor relative to result image, defaults to 0 (no scaling)
     */
    public function watermark(
        float $opacity = 0.5,
        ?Gravity $position = null,
        int $xOffset = 0,
        int $yOffset = 0,
        float $scale = 0
    ): self {
        if ($opacity < 0 || $opacity > 1) {
            throw new \InvalidArgumentException('Watermark opacity must be between 0.0 and 1.0');
        }

        if ($scale < 0) {
            throw new \InvalidArgumentException('Watermark scale must be 0 or greater');
        }

        $position = $position ?? Gravity::CENTER;
        $this->options['wm'] = "{$opacity}:{$position->value}:{$xOffset}:{$yOffset}:{$scale}";

        return $this;
    }

    /**
     * Use a custom watermark from URL.
     *
     * The URL should be base64 encoded.
     *
     * @param  string  $url  URL of the custom watermark image
     */
    public function watermarkUrl(string $url): self
    {
        $encodedUrl = rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
        $this->options['wmu'] = $encodedUrl;

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

    private function validateSourceUrl(): void
    {
        if (empty($this->source_url)) {
            throw new InvalidArgumentException('Invalid source URL');
        }

        if (! str_starts_with($this->source_url, 's3://') && ! filter_var($this->source_url, FILTER_VALIDATE_URL)) {
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
        return implode('/', array_map(
            fn ($key, $value) => "{$key}:{$value}",
            array_keys($this->options),
            $this->options
        ));
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
