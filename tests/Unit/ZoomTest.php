<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Zoom', function () {
    it('sets zoom with single value', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->zoom(2)
            ->build();

        expect($url)->toContain('z:2');
    });

    it('sets zoom with separate x and y values', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->zoom(2, 1.5)
            ->build();

        expect($url)->toContain('z:2:1.5');
    });

    it('accepts float values', function () {
        $url = imgproxy($this->sample_image_url)
            ->zoom(1.5)
            ->build();

        expect($url)->toContain('z:1.5');
    });

    it('validates zoom is greater than 0', function () {
        expect(function () {
            $this->imgProxy->zoom(0);
        })->toThrow(\InvalidArgumentException::class, 'Zoom must be greater than 0');
    });

    it('works with other options', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->zoom(1.5)
            ->setQuality(85)
            ->build();

        expect($url)->toContain('z:1.5')
            ->and($url)->toContain('width:300');
    });
});
