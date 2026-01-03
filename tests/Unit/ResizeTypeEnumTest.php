<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\ResizeType;

describe('ResizeType Enum', function () {
    it('has correct fit value', function () {
        expect(ResizeType::FIT->value)->toBe('fit');
    });

    it('has correct fill value', function () {
        expect(ResizeType::FILL->value)->toBe('fill');
    });

    it('has correct fill-down value', function () {
        expect(ResizeType::FILL_DOWN->value)->toBe('fill-down');
    });

    it('has correct force value', function () {
        expect(ResizeType::FORCE->value)->toBe('force');
    });

    it('has correct auto value', function () {
        expect(ResizeType::AUTO->value)->toBe('auto');
    });

    it('has fit as default', function () {
        expect(ResizeType::getDefault())->toBe(ResizeType::FIT);
    });

    it('returns correct short codes', function () {
        expect(ResizeType::FIT->getShortCode())->toBe('rt:fit');
        expect(ResizeType::FILL->getShortCode())->toBe('rt:fill');
        expect(ResizeType::FILL_DOWN->getShortCode())->toBe('rt:fill-down');
        expect(ResizeType::FORCE->getShortCode())->toBe('rt:force');
        expect(ResizeType::AUTO->getShortCode())->toBe('rt:auto');
    });

    it('returns correct full codes', function () {
        expect(ResizeType::FIT->getFullCode())->toBe('resizing_type:fit');
        expect(ResizeType::FILL->getFullCode())->toBe('resizing_type:fill');
        expect(ResizeType::FILL_DOWN->getFullCode())->toBe('resizing_type:fill-down');
        expect(ResizeType::FORCE->getFullCode())->toBe('resizing_type:force');
        expect(ResizeType::AUTO->getFullCode())->toBe('resizing_type:auto');
    });

    it('returns correct descriptions', function () {
        expect(ResizeType::FIT->getDescription())->toBe('Resizes the image while keeping aspect ratio to fit a given size.');
        expect(ResizeType::FILL->getDescription())->toBe('Resizes the image while keeping aspect ratio to fill a given size and crops projecting parts.');
        expect(ResizeType::FILL_DOWN->getDescription())->toBe('Same as fill, but if the resized image is smaller than the requested size, imgproxy will crop the result to keep the requested aspect ratio.');
        expect(ResizeType::FORCE->getDescription())->toBe('Resizes the image without keeping the aspect ratio.');
        expect(ResizeType::AUTO->getDescription())->toBe('If both source and resulting dimensions have the same orientation (portrait or landscape), imgproxy will use fill. Otherwise, it will use fit.');
    });

    it('has all five resize type cases', function () {
        $cases = ResizeType::cases();
        expect(count($cases))->toBe(5);
    });

    describe('fromString()', function () {
        it('creates ResizeType from lowercase string', function () {
            expect(ResizeType::fromString('fit'))->toBe(ResizeType::FIT);
            expect(ResizeType::fromString('fill'))->toBe(ResizeType::FILL);
            expect(ResizeType::fromString('fill-down'))->toBe(ResizeType::FILL_DOWN);
            expect(ResizeType::fromString('force'))->toBe(ResizeType::FORCE);
            expect(ResizeType::fromString('auto'))->toBe(ResizeType::AUTO);
        });

        it('creates ResizeType from uppercase string', function () {
            expect(ResizeType::fromString('FIT'))->toBe(ResizeType::FIT);
            expect(ResizeType::fromString('FILL'))->toBe(ResizeType::FILL);
            expect(ResizeType::fromString('FORCE'))->toBe(ResizeType::FORCE);
        });

        it('returns null for invalid string', function () {
            expect(ResizeType::fromString('invalid'))->toBeNull();
            expect(ResizeType::fromString(''))->toBeNull();
        });
    });
});
