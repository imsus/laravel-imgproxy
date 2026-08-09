<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Enums;

/**
 * The imgproxy watermark position (`wm` option).
 *
 * @see https://docs.imgproxy.net/usage/processing#watermark
 */
enum WatermarkPosition: string
{
    /**
     * Center of the image (the default).
     */
    case Center = 'ce';

    /**
     * North (top edge).
     */
    case North = 'no';

    /**
     * South (bottom edge).
     */
    case South = 'so';

    /**
     * East (right edge).
     */
    case East = 'ea';

    /**
     * West (left edge).
     */
    case West = 'we';

    /**
     * North-west (top-left corner).
     */
    case NorthWest = 'nowe';

    /**
     * North-east (top-right corner).
     */
    case NorthEast = 'noea';

    /**
     * South-west (bottom-left corner).
     */
    case SouthWest = 'sowe';

    /**
     * South-east (bottom-right corner).
     */
    case SouthEast = 'soea';

    /**
     * Repeat and tile the watermark to fill the entire image.
     */
    case Repeat = 're';
}
