<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Rotation;

describe('Rotation Enum', function () {
    it('has correct deg 0 value', function () {
        expect(Rotation::DEG_0->value)->toBe(0);
    });

    it('has correct deg 90 value', function () {
        expect(Rotation::DEG_90->value)->toBe(90);
    });

    it('has correct deg 180 value', function () {
        expect(Rotation::DEG_180->value)->toBe(180);
    });

    it('has correct deg 270 value', function () {
        expect(Rotation::DEG_270->value)->toBe(270);
    });

    it('has all four rotation cases', function () {
        $cases = Rotation::cases();
        expect(count($cases))->toBe(4);
    });

    describe('fromString()', function () {
        it('creates Rotation from string integer', function () {
            expect(Rotation::fromString('0'))->toBe(Rotation::DEG_0);
            expect(Rotation::fromString('90'))->toBe(Rotation::DEG_90);
            expect(Rotation::fromString('180'))->toBe(Rotation::DEG_180);
            expect(Rotation::fromString('270'))->toBe(Rotation::DEG_270);
        });

        it('returns null for invalid string', function () {
            expect(Rotation::fromString('45'))->toBeNull();
            expect(Rotation::fromString('invalid'))->toBeNull();
            expect(Rotation::fromString(''))->toBeNull();
        });
    });
});
