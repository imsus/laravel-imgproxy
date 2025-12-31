<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Gravity Methods', function () {
    it('can set gravity with center', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::CENTER)
            ->build();

        expect($url)->toContain('gravity:ce');
    });

    it('can set gravity with north east', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::NORTH_EAST)
            ->build();

        expect($url)->toContain('gravity:ne');
    });

    it('can set gravity with south west', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::SOUTH_WEST)
            ->build();

        expect($url)->toContain('gravity:sw');
    });

    it('can set gravity with north', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::NORTH)
            ->build();

        expect($url)->toContain('gravity:n');
    });

    it('can set gravity with east', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::EAST)
            ->build();

        expect($url)->toContain('gravity:e');
    });

    it('can set gravity with west', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::WEST)
            ->build();

        expect($url)->toContain('gravity:w');
    });

    it('can set gravity with south', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::SOUTH)
            ->build();

        expect($url)->toContain('gravity:s');
    });

    it('returns self for method chaining', function () {
        $imgProxy = new ImgProxy;
        $result = $imgProxy->setGravity(Gravity::CENTER);

        expect($result)->toBe($imgProxy);
    });

    it('can crop with default gravity (center)', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(300, 200)
            ->build();

        expect($url)->toContain('crop:300:200:ce');
    });

    it('can crop with custom gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(300, 200, Gravity::NORTH_EAST)
            ->build();

        expect($url)->toContain('crop:300:200:ne');
    });

    it('can crop with south west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(100, 100, Gravity::SOUTH_WEST)
            ->build();

        expect($url)->toContain('crop:100:100:sw');
    });

    it('can crop with north west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(150, 150, Gravity::NORTH_WEST)
            ->build();

        expect($url)->toContain('crop:150:150:nw');
    });

    it('can crop with east gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(200, 300, Gravity::EAST)
            ->build();

        expect($url)->toContain('crop:200:300:e');
    });

    it('can crop with west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(200, 300, Gravity::WEST)
            ->build();

        expect($url)->toContain('crop:200:300:w');
    });

    it('returns self for crop method chaining', function () {
        $imgProxy = new ImgProxy;
        $result = $imgProxy->crop(100, 100);

        expect($result)->toBe($imgProxy);
    });

    it('can chain gravity and crop', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(500)
            ->setHeight(500)
            ->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)
            ->setGravity(Gravity::NORTH_EAST)
            ->build();

        expect($url)->toContain('gravity:ne');
    });

    it('can use gravity with other options', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->setHeight(200)
            ->setQuality(85)
            ->setGravity(Gravity::SOUTH_EAST)
            ->build();

        expect($url)->toContain('width:300');
        expect($url)->toContain('height:200');
        expect($url)->toContain('quality:85');
        expect($url)->toContain('gravity:se');
    });

    it('can use crop with width and height', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(300, 300)
            ->setQuality(90)
            ->setBlur(1.0)
            ->build();

        expect($url)->toContain('crop:300:300:ce');
        expect($url)->toContain('quality:90');
        expect($url)->toContain('blur:1');
    });
});
