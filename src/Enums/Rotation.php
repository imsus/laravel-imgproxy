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
}
