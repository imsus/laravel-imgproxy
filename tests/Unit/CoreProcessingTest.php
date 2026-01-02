<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    // Enable short options for these tests
    config()->set('imgproxy.use_short_options', true);
});

describe('Core Processing', function () {
    describe('padding', function () {
        it('adds padding to the URL', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->padding(20)
                ->build();

            expect($url)->toContain('pd:20');
        });

        it('validates padding is non-negative', function () {
            expect(function () {
                $this->imgProxy->padding(-1);
            })->toThrow(\InvalidArgumentException::class, 'Padding must be 0 or greater');
        });

        it('accepts zero padding', function () {
            $this->imgProxy->padding(0);
            expect(true)->toBe(true);
        });

        it('accepts large padding values', function () {
            $url = imgproxy($this->sample_image_url)
                ->padding(100)
                ->build();

            expect($url)->toContain('pd:100');
        });
    });

    describe('background', function () {
        it('adds background color to the URL', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->background('FF5733')
                ->build();

            expect($url)->toContain('bg:FF5733');
        });

        it('validates hex color format', function () {
            expect(function () {
                $this->imgProxy->background('GG5733');
            })->toThrow(\InvalidArgumentException::class, 'Background must be a valid 6-digit hex color');
        });

        it('validates short hex color', function () {
            expect(function () {
                $this->imgProxy->background('FF57');
            })->toThrow(\InvalidArgumentException::class, 'Background must be a valid 6-digit hex color');
        });

        it('validates empty string', function () {
            expect(function () {
                $this->imgProxy->background('');
            })->toThrow(\InvalidArgumentException::class, 'Background must be a valid 6-digit hex color');
        });

        it('accepts lowercase hex colors', function () {
            $url = imgproxy($this->sample_image_url)
                ->background('aabbcc')
                ->build();

            expect($url)->toContain('bg:aabbcc');
        });
    });

    describe('autoRotate', function () {
        it('enables auto-rotate by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->autoRotate()
                ->build();

            expect($url)->toContain('ar:1');
        });

        it('can disable auto-rotate', function () {
            $url = imgproxy($this->sample_image_url)
                ->autoRotate(false)
                ->build();

            expect($url)->toContain('ar:0');
        });

        it('can explicitly enable auto-rotate', function () {
            $url = imgproxy($this->sample_image_url)
                ->autoRotate(true)
                ->build();

            expect($url)->toContain('ar:1');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->autoRotate(true)
                ->setQuality(85)
                ->build();

            expect($url)->toContain('ar:1')
                ->and($url)->toContain('w:300')
                ->and($url)->toContain('q:85');
        });
    });

    describe('rotate', function () {
        it('adds rotation to the URL', function () {
            $url = imgproxy($this->sample_image_url)
                ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_90)
                ->build();

            expect($url)->toContain('rot:90');
        });

        it('accepts 0 degrees', function () {
            $url = imgproxy($this->sample_image_url)
                ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_0)
                ->build();

            expect($url)->toContain('rot:0');
        });

        it('accepts 180 degrees', function () {
            $url = imgproxy($this->sample_image_url)
                ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_180)
                ->build();

            expect($url)->toContain('rot:180');
        });

        it('accepts 270 degrees', function () {
            $url = imgproxy($this->sample_image_url)
                ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_270)
                ->build();

            expect($url)->toContain('rot:270');
        });
    });

    describe('stripMetadata', function () {
        it('strips metadata by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->stripMetadata()
                ->build();

            expect($url)->toContain('sm:1');
        });

        it('can disable metadata stripping', function () {
            $url = imgproxy($this->sample_image_url)
                ->stripMetadata(false)
                ->build();

            expect($url)->toContain('sm:0');
        });

        it('can explicitly enable metadata stripping', function () {
            $url = imgproxy($this->sample_image_url)
                ->stripMetadata(true)
                ->build();

            expect($url)->toContain('sm:1');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->stripMetadata()
                ->setQuality(90)
                ->build();

            expect($url)->toContain('sm:1')
                ->and($url)->toContain('w:300')
                ->and($url)->toContain('q:90');
        });
    });

    describe('trim', function () {
        it('adds trim with default threshold', function () {
            $url = imgproxy($this->sample_image_url)
                ->trim()
                ->build();

            expect($url)->toContain('t:10');
        });

        it('adds trim with custom threshold', function () {
            $url = imgproxy($this->sample_image_url)
                ->trim(20)
                ->build();

            expect($url)->toContain('t:20');
        });

        it('validates threshold is non-negative', function () {
            expect(function () {
                $this->imgProxy->trim(-1);
            })->toThrow(\InvalidArgumentException::class, 'Trim threshold must be 0 or greater');
        });

        it('accepts zero threshold', function () {
            $url = imgproxy($this->sample_image_url)
                ->trim(0)
                ->build();

            expect($url)->toContain('t:0');
        });

        it('accepts large threshold values', function () {
            $url = imgproxy($this->sample_image_url)
                ->trim(100)
                ->build();

            expect($url)->toContain('t:100');
        });
    });

    describe('pixelate', function () {
        it('adds pixelation to the URL', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->pixelate(10)
                ->build();

            expect($url)->toContain('pix:10');
        });

        it('validates pixel size is non-negative', function () {
            expect(function () {
                $this->imgProxy->pixelate(-1);
            })->toThrow(\InvalidArgumentException::class, 'Pixel size must be 0 or greater');
        });

        it('accepts zero pixel size', function () {
            $url = imgproxy($this->sample_image_url)
                ->pixelate(0)
                ->build();

            expect($url)->toContain('pix:0');
        });

        it('accepts large pixel sizes', function () {
            $url = imgproxy($this->sample_image_url)
                ->pixelate(50)
                ->build();

            expect($url)->toContain('pix:50');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->pixelate(15)
                ->setQuality(80)
                ->build();

            expect($url)->toContain('pix:15')
                ->and($url)->toContain('w:300')
                ->and($url)->toContain('q:80');
        });
    });

    describe('combined usage', function () {
        it('can use multiple processing options together', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(400)
                ->setHeight(300)
                ->padding(20)
                ->background('FF5733')
                ->rotate(\Imsus\ImgProxy\Enums\Rotation::DEG_90)
                ->pixelate(8)
                ->build();

            expect($url)->toContain('w:400')
                ->and($url)->toContain('h:300')
                ->and($url)->toContain('pd:20')
                ->and($url)->toContain('bg:FF5733')
                ->and($url)->toContain('rot:90')
                ->and($url)->toContain('pix:8');
        });

        it('can use all boolean processing options', function () {
            $url = imgproxy($this->sample_image_url)
                ->autoRotate(true)
                ->stripMetadata(true)
                ->build();

            expect($url)->toContain('ar:1')
                ->and($url)->toContain('sm:1');
        });
    });
});
