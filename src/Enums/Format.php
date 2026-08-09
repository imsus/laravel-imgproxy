<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Enums;

/**
 * The imgproxy result image format (`f` option).
 *
 * @see https://docs.imgproxy.net/usage/processing#format
 */
enum Format: string
{
    /**
     * JPEG.
     */
    case Jpg = 'jpg';

    /**
     * PNG.
     */
    case Png = 'png';

    /**
     * WebP.
     */
    case Webp = 'webp';

    /**
     * AVIF.
     */
    case Avif = 'avif';

    /**
     * GIF.
     */
    case Gif = 'gif';

    /**
     * ICO.
     */
    case Ico = 'ico';

    /**
     * SVG.
     */
    case Svg = 'svg';

    /**
     * BMP.
     */
    case Bmp = 'bmp';

    /**
     * TIFF.
     */
    case Tiff = 'tiff';

    /**
     * HEIC.
     */
    case Heic = 'heic';

    /**
     * JPEG XL.
     */
    case Jxl = 'jxl';
}
