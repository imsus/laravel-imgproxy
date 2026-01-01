<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Watermark Enhancements', function () {
    describe('watermark with repeat position', function () {
        it('sets watermark with repeat position', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.5, 'repeat')
                ->build();

            expect($url)->toContain('wm:0.5:re:0:0:0');
        });
    });

    describe('watermark with chessboard position', function () {
        it('sets watermark with chessboard position', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.5, 'chessboard')
                ->build();

            expect($url)->toContain('wm:0.5:ch:0:0:0');
        });
    });

    describe('watermark with relative offsets', function () {
        it('accepts float offsets', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.5, Gravity::CENTER, 0.5, 0.25)
                ->build();

            expect($url)->toContain('wm:0.5:ce:0.5:0.25:0');
        });

        it('accepts negative float offsets', function () {
            $url = imgproxy($this->sample_image_url)
                ->watermark(0.5, Gravity::CENTER, -0.1, 0.1)
                ->build();

            expect($url)->toContain('wm:0.5:ce:-0.1:0.1:0');
        });
    });
});
