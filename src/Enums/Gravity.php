<?php

namespace Imsus\ImgProxy\Enums;

enum Gravity: string
{
    case CENTER = 'ce';
    case NORTH = 'no';
    case SOUTH = 'so';
    case EAST = 'ea';
    case WEST = 'we';
    case NORTH_EAST = 'noea';
    case SOUTH_EAST = 'soea';
    case SOUTH_WEST = 'sowe';
    case NORTH_WEST = 'nowe';
    case SMART = 'sm';

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
            self::SMART => 'Smart (auto-detect interesting section)',
        };
    }
}
