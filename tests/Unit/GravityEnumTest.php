<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Gravity;

describe('Gravity Enum', function () {
    it('has correct center value', function () {
        expect(Gravity::CENTER->value)->toBe('ce');
    });

    it('has correct north value', function () {
        expect(Gravity::NORTH->value)->toBe('n');
    });

    it('has correct south value', function () {
        expect(Gravity::SOUTH->value)->toBe('s');
    });

    it('has correct east value', function () {
        expect(Gravity::EAST->value)->toBe('e');
    });

    it('has correct west value', function () {
        expect(Gravity::WEST->value)->toBe('w');
    });

    it('has correct north east value', function () {
        expect(Gravity::NORTH_EAST->value)->toBe('ne');
    });

    it('has correct south east value', function () {
        expect(Gravity::SOUTH_EAST->value)->toBe('se');
    });

    it('has correct south west value', function () {
        expect(Gravity::SOUTH_WEST->value)->toBe('sw');
    });

    it('has correct north west value', function () {
        expect(Gravity::NORTH_WEST->value)->toBe('nw');
    });

    it('has center as default', function () {
        expect(Gravity::getDefault())->toBe(Gravity::CENTER);
    });

    it('returns correct descriptions', function () {
        expect(Gravity::CENTER->getDescription())->toBe('Center');
        expect(Gravity::NORTH->getDescription())->toBe('North (top center)');
        expect(Gravity::SOUTH->getDescription())->toBe('South (bottom center)');
        expect(Gravity::EAST->getDescription())->toBe('East (right center)');
        expect(Gravity::WEST->getDescription())->toBe('West (left center)');
        expect(Gravity::NORTH_EAST->getDescription())->toBe('North East (top right)');
        expect(Gravity::SOUTH_EAST->getDescription())->toBe('South East (bottom right)');
        expect(Gravity::SOUTH_WEST->getDescription())->toBe('South West (bottom left)');
        expect(Gravity::NORTH_WEST->getDescription())->toBe('North West (top left)');
    });

    it('has all nine gravity cases', function () {
        $cases = Gravity::cases();
        expect(count($cases))->toBe(9);
    });
});
