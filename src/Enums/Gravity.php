<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Enums;

/**
 * The imgproxy gravity type (`g` option).
 *
 * Values match imgproxy v4 (4.0.x); verified against the v4 processing docs
 * and imgproxy master eef3b31 (2026-08-06). Re-verify when upgrading imgproxy.
 *
 * @see https://docs.imgproxy.net/usage/processing#gravity
 */
enum Gravity: string
{
    /**
     * Center of the image.
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
     * Smart gravity: libvips detects the most interesting section of the image.
     */
    case Smart = 'sm';
}
