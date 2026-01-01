<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Padding', function () {
    describe('paddingAll', function () {
        it('sets padding for all sides', function () {
            $url = imgproxy($this->sample_image_url)
                ->paddingAll(10)
                ->build();

            expect($url)->toContain('pd:10');
        });

        it('sets vertical and horizontal padding', function () {
            $url = imgproxy($this->sample_image_url)
                ->paddingAll(10, 20)
                ->build();

            expect($url)->toContain('pd:10:20');
        });

        it('sets top, horizontal, and bottom padding', function () {
            $url = imgproxy($this->sample_image_url)
                ->paddingAll(10, 20, 30)
                ->build();

            expect($url)->toContain('pd:10:20:30');
        });

        it('sets all four sides padding', function () {
            $url = imgproxy($this->sample_image_url)
                ->paddingAll(10, 20, 30, 40)
                ->build();

            expect($url)->toContain('pd:10:20:30:40');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->paddingAll(10);

            expect($result)->toBe($this->imgProxy);
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->paddingAll(15)
                ->setQuality(85)
                ->build();

            expect($url)->toContain('pd:15')
                ->and($url)->toContain('width:300')
                ->and($url)->toContain('quality:85');
        });

        it('accepts zero padding', function () {
            $url = imgproxy($this->sample_image_url)
                ->paddingAll(0)
                ->build();

            expect($url)->toContain('pd:0');
        });
    });
});
