<?php

namespace Imsus\ImgProxy\Components;

use Illuminate\View\Component;
use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

class Img extends Component
{
    /**
     * Create a new component instance.
     *
     * @param  string  $src  Source image URL
     * @param  string|null  $alt  Alt text for accessibility
     * @param  int|null  $width  Image width (alias: w)
     * @param  int|null  $height  Image height (alias: h)
     * @param  ResizeType|null  $resizeType  Resize mode (alias: fit)
     * @param  OutputExtension|null  $format  Output format (alias: fmt)
     * @param  int  $quality  Image quality 0-100 (alias: q)
     * @param  int|null  $dpr  Device pixel ratio
     * @param  Gravity|null  $gravity  Gravity position (alias: grav)
     * @param  bool  $lazy  Enable lazy loading
     * @param  string|null  $sizes  Sizes attribute for responsive images
     * @param  int|null  $w  Short alias for width
     * @param  int|null  $h  Short alias for height
     * @param  int|null  $q  Short alias for quality
     * @param  ResizeType|null  $fit  Short alias for resizeType
     * @param  OutputExtension|null  $fmt  Short alias for format
     * @param  Gravity|null  $grav  Short alias for gravity
     */
    public function __construct(
        public string $src,
        public ?string $alt = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?ResizeType $resizeType = null,
        public ?OutputExtension $format = null,
        public int $quality = 75,
        public ?int $dpr = null,
        public ?Gravity $gravity = null,
        public bool $lazy = true,
        public ?string $sizes = null,
        // Short property aliases
        public ?int $w = null,
        public ?int $h = null,
        public ?int $q = 75,
        public ?ResizeType $fit = null,
        public ?OutputExtension $fmt = null,
        public ?Gravity $grav = null,
    ) {}

    /**
     * Build the ImgProxy URL from the component attributes.
     */
    public function buildUrl(): string
    {
        $url = imgproxy($this->src);

        // Use alias properties with coalesce - original properties take precedence
        $width = $this->width ?? $this->w;
        $height = $this->height ?? $this->h;
        $quality = $this->quality !== 75 ? $this->quality : ($this->q ?? 75);
        $resizeType = $this->resizeType ?? $this->fit;
        $format = $this->format ?? $this->fmt;
        $gravity = $this->gravity ?? $this->grav;

        if ($width !== null) {
            $url->width($width);
        }

        if ($height !== null) {
            $url->height($height);
        }

        if ($resizeType !== null) {
            $url->setResizeType($resizeType);
        }

        if ($format !== null) {
            $url->setExtension($format);
        }

        if ($quality > 0) {
            $url->quality($quality);
        }

        if ($this->dpr !== null) {
            $url->dpr($this->dpr);
        }

        if ($gravity !== null) {
            $url->setGravity($gravity);
        }

        return $url->build();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('imgproxy::img');
    }
}
