<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Enums;

/**
 * The imgproxy resizing type (`rs` option).
 *
 * @see https://docs.imgproxy.net/usage/processing#resizing-type
 */
enum ResizeType: string
{
    /**
     * Resize the image while keeping the aspect ratio to fit the given size.
     */
    case Fit = 'fit';

    /**
     * Resize the image while keeping the aspect ratio to fill the given size and crop the projecting parts.
     */
    case Fill = 'fill';

    /**
     * Like `fill`, but crop the result to the requested aspect ratio when the resized image is smaller.
     */
    case FillDown = 'fill-down';

    /**
     * Resize the image without keeping the aspect ratio.
     */
    case Force = 'force';

    /**
     * Use `fill` when the source and resulting dimensions have the same orientation, otherwise `fit`.
     */
    case Auto = 'auto';
}
