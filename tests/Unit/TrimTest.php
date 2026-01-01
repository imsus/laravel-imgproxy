<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Trim', function () {
    describe('trimWithColor', function () {
        it('adds trim with hex color', function () {
            $url = imgproxy($this->sample_image_url)
                ->trimWithColor(10, 'FF5733')
                ->build();

            expect($url)->toContain('trim:10:FF5733');
        });

        it('adds trim with color and horizontal equalization', function () {
            $url = imgproxy($this->sample_image_url)
                ->trimWithColor(10, 'FF5733', true, false)
                ->build();

            expect($url)->toContain('trim:10:FF5733:1:0');
        });

        it('adds trim with color and vertical equalization', function () {
            $url = imgproxy($this->sample_image_url)
                ->trimWithColor(10, 'FF5733', false, true)
                ->build();

            expect($url)->toContain('trim:10:FF5733:0:1');
        });

        it('adds trim with both equalizations', function () {
            $url = imgproxy($this->sample_image_url)
                ->trimWithColor(10, 'FF5733', true, true)
                ->build();

            expect($url)->toContain('trim:10:FF5733:1:1');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->trimWithColor(10, 'FF5733');

            expect($result)->toBe($this->imgProxy);
        });
    });
});
