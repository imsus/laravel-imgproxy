<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
});

describe('Validation', function () {
    it('validates dpr bounds', function () {
        expect(function () {
            $this->imgProxy->setDpr(0);
        })->toThrow(\InvalidArgumentException::class, 'DPR (Device Pixel Ratio) must be between 1 and 8');

        expect(function () {
            $this->imgProxy->setDpr(9);
        })->toThrow(\InvalidArgumentException::class, 'DPR (Device Pixel Ratio) must be between 1 and 8');
    });

    it('accepts valid dpr values', function () {
        // Should not throw exceptions
        $this->imgProxy->setDpr(1);
        $this->imgProxy->setDpr(4);
        $this->imgProxy->setDpr(8);

        expect(true)->toBe(true);
    });

    it('validates quality bounds', function () {
        expect(function () {
            $this->imgProxy->setQuality(-1);
        })->toThrow(\InvalidArgumentException::class, 'Quality must be between 0 and 100');

        expect(function () {
            $this->imgProxy->setQuality(101);
        })->toThrow(\InvalidArgumentException::class, 'Quality must be between 0 and 100');
    });

    it('accepts valid quality values', function () {
        $this->imgProxy->setQuality(0);
        $this->imgProxy->setQuality(50);
        $this->imgProxy->setQuality(100);

        expect(true)->toBe(true);
    });

    it('validates blur sigma values', function () {
        expect(function () {
            $this->imgProxy->setBlur(-1.0);
        })->toThrow(\InvalidArgumentException::class, 'Blur sigma must be 0.0 or greater');
    });

    it('validates sharpen sigma values', function () {
        expect(function () {
            $this->imgProxy->setSharpen(-1.0);
        })->toThrow(\InvalidArgumentException::class, 'Sharpen sigma must be 0.0 or greater');
    });

    it('validates brightness bounds', function () {
        expect(function () {
            $this->imgProxy->setBrightness(-256);
        })->toThrow(\InvalidArgumentException::class, 'Brightness must be between -255 and 255');

        expect(function () {
            $this->imgProxy->setBrightness(256);
        })->toThrow(\InvalidArgumentException::class, 'Brightness must be between -255 and 255');
    });

    it('validates contrast values', function () {
        expect(function () {
            $this->imgProxy->setContrast(-1.0);
        })->toThrow(\InvalidArgumentException::class, 'Contrast must be 0.0 or greater');
    });

    it('validates saturation values', function () {
        expect(function () {
            $this->imgProxy->setSaturation(-1.0);
        })->toThrow(\InvalidArgumentException::class, 'Saturation must be 0.0 or greater');
    });
});
