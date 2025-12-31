<?php

namespace Imsus\ImgProxy\Enums;

enum OutputExtension: string
{
    case PNG = 'png';
    case JPEG = 'jpg';
    case JPEG_XL = 'jxl';
    case WEBP = 'webp';
    case AVIF = 'avif';
    case GIF = 'gif';
    case ICO = 'ico';
    case SVG = 'svg';
    case HEIC = 'heic';
    case BMP = 'bmp';
    case TIFF = 'tiff';

    public static function getDefault(): self
    {
        return self::JPEG;
    }

    public static function fromExtension(string $extension): ?self
    {
        return self::tryFrom(strtolower($extension));
    }
}
