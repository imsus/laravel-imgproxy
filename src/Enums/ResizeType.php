<?php

namespace Imsus\ImgProxy\Enums;

enum ResizeType: string
{
    case FIT = 'fit';
    case FILL = 'fill';
    case FILL_DOWN = 'fill-down';
    case FORCE = 'force';
    case AUTO = 'auto';

    public function getShortCode(): string
    {
        return match ($this) {
            self::FIT => 'rt:fit',
            self::FILL => 'rt:fill',
            self::FILL_DOWN => 'rt:fill-down',
            self::FORCE => 'rt:force',
            self::AUTO => 'rt:auto',
        };
    }

    public function getFullCode(): string
    {
        return match ($this) {
            self::FIT => 'resizing_type:fit',
            self::FILL => 'resizing_type:fill',
            self::FILL_DOWN => 'resizing_type:fill-down',
            self::FORCE => 'resizing_type:force',
            self::AUTO => 'resizing_type:auto',
        };
    }

    public static function getDefault(): self
    {
        return self::FIT;
    }

    /**
     * Get the description of the resizing type.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::FIT => 'Resizes the image while keeping aspect ratio to fit a given size.',
            self::FILL => 'Resizes the image while keeping aspect ratio to fill a given size and crops projecting parts.',
            self::FILL_DOWN => 'Same as fill, but if the resized image is smaller than the requested size, imgproxy will crop the result to keep the requested aspect ratio.',
            self::FORCE => 'Resizes the image without keeping the aspect ratio.',
            self::AUTO => 'If both source and resulting dimensions have the same orientation (portrait or landscape), imgproxy will use fill. Otherwise, it will use fit.',
        };
    }

    /**
     * Create a ResizeType from a string value (case-insensitive).
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom(strtolower($value));
    }
}
