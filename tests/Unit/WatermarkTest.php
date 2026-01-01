<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Watermarking', function () {
    describe('watermark', function () {
        it('adds watermark with default values', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->watermark()
                ->build();

            // Default: 0.5:ce:0:0:0
            expect($url)->toContain('wm:0.5:ce:0:0:0');
        });

        it('sets watermark opacity', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.7)
                ->build();

            expect($url)->toContain('wm:0.7:ce:0:0:0');
        });

        it('sets watermark position', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.5, Gravity::SOUTH_EAST)
                ->build();

            expect($url)->toContain('wm:0.5:soea:0:0:0');
        });

        it('sets watermark with all options', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.7, Gravity::NORTH_WEST, 10, 20, 0.3)
                ->build();

            expect($url)->toContain('wm:0.7:nowe:10:20:0.3');
        });

        it('validates opacity bounds - negative', function () {
            expect(function () {
                $this->imgProxy->watermark(-0.1);
            })->toThrow(\InvalidArgumentException::class, 'Watermark opacity must be between 0.0 and 1.0');
        });

        it('validates opacity bounds - exceeds 1', function () {
            expect(function () {
                $this->imgProxy->watermark(1.1);
            })->toThrow(\InvalidArgumentException::class, 'Watermark opacity must be between 0.0 and 1.0');
        });

        it('accepts 0.0 opacity', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.0)
                ->build();

            expect($url)->toContain('wm:0:ce:0:0:0');
        });

        it('accepts 1.0 opacity', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(1.0)
                ->build();

            expect($url)->toContain('wm:1:ce:0:0:0');
        });

        it('validates scale is not negative', function () {
            expect(function () {
                $this->imgProxy->watermark(0.5, Gravity::CENTER, 0, 0, -1);
            })->toThrow(\InvalidArgumentException::class, 'Watermark scale must be 0 or greater');
        });

        it('accepts all gravity positions', function () {
            $positions = [
                [Gravity::CENTER, 'ce'],
                [Gravity::NORTH, 'no'],
                [Gravity::SOUTH, 'so'],
                [Gravity::EAST, 'ea'],
                [Gravity::WEST, 'we'],
                [Gravity::NORTH_EAST, 'noea'],
                [Gravity::NORTH_WEST, 'nowe'],
                [Gravity::SOUTH_EAST, 'soea'],
                [Gravity::SOUTH_WEST, 'sowe'],
            ];

            foreach ($positions as [$gravity, $code]) {
                $url = imgproxy($this->sample_image_url)
                    ->watermark(0.5, $gravity)
                    ->build();

                expect($url)->toContain("wm:0.5:{$code}:");
            }
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(500)
                ->setHeight(400)
                ->watermark(0.7, Gravity::SOUTH_EAST, 0, 0, 0.25)
                ->setQuality(85)
                ->build();

            expect($url)->toContain('wm:0.7:soea:0:0:0.25')
                ->and($url)->toContain('width:500')
                ->and($url)->toContain('quality:85');
        });
    });
});
