<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

describe('Exceptions', function () {
    it('throws exception for invalid hex key', function () {
        expect(function () {
            config()->set('imgproxy.key', 'invalid-hex');
            new ImgProxy;
        })->toThrow(\InvalidArgumentException::class, 'The key must be a hex-encoded string.');
    });

    it('throws exception for invalid hex salt', function () {
        expect(function () {
            config()->set('imgproxy.salt', 'invalid-hex');
            new ImgProxy;
        })->toThrow(\InvalidArgumentException::class, 'The salt must be a hex-encoded string.');
    });
});
