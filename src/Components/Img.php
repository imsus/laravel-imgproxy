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
    ) {}

    /**
     * Build the ImgProxy URL from the component attributes.
     */
    public function buildUrl(): string
    {
        $url = imgproxy($this->src);

        if ($this->width !== null) {
            $url->setWidth($this->width);
        }

        if ($this->height !== null) {
            $url->setHeight($this->height);
        }

        if ($this->resizeType !== null) {
            $url->setResizeType($this->resizeType);
        }

        if ($this->format !== null) {
            $url->setExtension($this->format);
        }

        if ($this->quality > 0) {
            $url->setQuality($this->quality);
        }

        if ($this->dpr !== null) {
            $url->setDpr($this->dpr);
        }

        if ($this->gravity !== null) {
            $url->setGravity($this->gravity);
        }

        return $url->build();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('imgproxy::img');
    }
}
