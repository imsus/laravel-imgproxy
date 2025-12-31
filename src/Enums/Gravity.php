<?php

namespace Imsus\ImgProxy\Enums;

enum Gravity: string
{
    case CENTER = 'ce';
    case NORTH = 'n';
    case SOUTH = 's';
    case EAST = 'e';
    case WEST = 'w';
    case NORTH_EAST = 'ne';
    case SOUTH_EAST = 'se';
    case SOUTH_WEST = 'sw';
    case NORTH_WEST = 'nw';

    public static function getDefault(): self
    {
        return self::CENTER;
    }

    /**
     * Get the description of the gravity position.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::CENTER => 'Center',
            self::NORTH => 'North (top center)',
            self::SOUTH => 'South (bottom center)',
            self::EAST => 'East (right center)',
            self::WEST => 'West (left center)',
            self::NORTH_EAST => 'North East (top right)',
            self::SOUTH_EAST => 'South East (bottom right)',
            self::SOUTH_WEST => 'South West (bottom left)',
            self::NORTH_WEST => 'North West (top left)',
        };
    }
}
