<?php

namespace Imsus\ImgProxy\Enums;

enum Rotation: int
{
    case DEG_0 = 0;
    case DEG_90 = 90;
    case DEG_180 = 180;
    case DEG_270 = 270;

    public function value(): int
    {
        return $this->value;
    }

    /**
     * Create a Rotation from a string value (parses integer string).
     */
    public static function fromString(string $value): ?self
    {
        // Validate that the string is a valid integer representation
        if (! ctype_digit($value) && $value !== '0') {
            return null;
        }

        return self::tryFrom((int) $value);
    }
}
