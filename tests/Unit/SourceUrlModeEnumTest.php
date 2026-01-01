<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\SourceUrlMode;

describe('SourceUrlMode Enum', function () {
    it('has correct plain value', function () {
        expect(SourceUrlMode::PLAIN->value)->toBe('plain');
    });

    it('has correct encoded value', function () {
        expect(SourceUrlMode::ENCODED->value)->toBe('encoded');
    });

    it('has encoded as default', function () {
        expect(SourceUrlMode::getDefault())->toBe(SourceUrlMode::ENCODED);
    });

    it('has both source url mode cases', function () {
        $cases = SourceUrlMode::cases();
        expect(count($cases))->toBe(2);
    });

    it('creates from valid string', function () {
        expect(SourceUrlMode::fromString('plain'))->toBe(SourceUrlMode::PLAIN);
        expect(SourceUrlMode::fromString('encoded'))->toBe(SourceUrlMode::ENCODED);
    });

    it('creates from valid string case insensitive', function () {
        expect(SourceUrlMode::fromString('PLAIN'))->toBe(SourceUrlMode::PLAIN);
        expect(SourceUrlMode::fromString('ENCODED'))->toBe(SourceUrlMode::ENCODED);
        expect(SourceUrlMode::fromString('Plain'))->toBe(SourceUrlMode::PLAIN);
        expect(SourceUrlMode::fromString('Encoded'))->toBe(SourceUrlMode::ENCODED);
    });

    it('returns null for invalid string', function () {
        expect(SourceUrlMode::fromString('invalid'))->toBeNull();
        expect(SourceUrlMode::fromString(''))->toBeNull();
        expect(SourceUrlMode::fromString('url'))->toBeNull();
    });
});
