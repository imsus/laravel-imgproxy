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

        expect($url)->toContain('gravity:noea');
    });

    it('can set gravity with south west', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::SOUTH_WEST)
            ->build();

        expect($url)->toContain('gravity:sowe');
    });

    it('can set gravity with north', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::NORTH)
            ->build();

        expect($url)->toContain('gravity:no');
    });

    it('can set gravity with east', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::EAST)
            ->build();

        expect($url)->toContain('gravity:ea');
    });

    it('can set gravity with west', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::WEST)
            ->build();

        expect($url)->toContain('gravity:we');
    });

    it('can set gravity with south', function () {
        $url = imgproxy($this->sample_image_url)
            ->setGravity(Gravity::SOUTH)
            ->build();

        expect($url)->toContain('gravity:so');
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

        expect($url)->toContain('crop:300:200:noea');
    });

    it('can crop with south west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(100, 100, Gravity::SOUTH_WEST)
            ->build();

        expect($url)->toContain('crop:100:100:sowe');
    });

    it('can crop with north west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(150, 150, Gravity::NORTH_WEST)
            ->build();

        expect($url)->toContain('crop:150:150:nowe');
    });

    it('can crop with east gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(200, 300, Gravity::EAST)
            ->build();

        expect($url)->toContain('crop:200:300:ea');
    });

    it('can crop with west gravity', function () {
        $url = imgproxy($this->sample_image_url)
            ->crop(200, 300, Gravity::WEST)
            ->build();

        expect($url)->toContain('crop:200:300:we');
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

        expect($url)->toContain('gravity:noea');
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
        expect($url)->toContain('gravity:soea');
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

    describe('gravity with offsets', function () {
        it('sets gravity with absolute offsets', function () {
            $url = imgproxy($this->sample_image_url)
                ->setGravityWithOffset(Gravity::CENTER, 10, 20)
                ->build();

            expect($url)->toContain('gravity:ce:10:20');
        });

        it('sets gravity with relative offsets (less than 1)', function () {
            $url = imgproxy($this->sample_image_url)
                ->setGravityWithOffset(Gravity::EAST, 0.5, 0.25)
                ->build();

            expect($url)->toContain('gravity:ea:0.5:0.25');
        });
    });

    describe('smart gravity', function () {
        it('sets smart gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setGravity(Gravity::SMART)
                ->build();

            expect($url)->toContain('gravity:sm');
        });
    });

    describe('focus point gravity', function () {
        it('sets focus point gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setFocusPoint(0.5, 0.75)
                ->build();

            expect($url)->toContain('gravity:fp:0.5:0.75');
        });

        it('accepts edge coordinates', function () {
            $url = imgproxy($this->sample_image_url)
                ->setFocusPoint(0, 1)
                ->build();

            expect($url)->toContain('gravity:fp:0:1');
        });
    });

    describe('crop with float values', function () {
        it('accepts float width for relative crop', function () {
            $url = imgproxy($this->sample_image_url)
                ->crop(0.5, 0.5)
                ->build();

            expect($url)->toContain('crop:0.5:0.5:ce');
        });

        it('accepts float height for relative crop', function () {
            $url = imgproxy($this->sample_image_url)
                ->crop(300, 0.75)
                ->build();

            expect($url)->toContain('crop:300:0.75:ce');
        });

        it('accepts zero for full source dimension', function () {
            $url = imgproxy($this->sample_image_url)
                ->crop(0, 0)
                ->build();

            expect($url)->toContain('crop:0:0:ce');
        });

        it('works with custom gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->crop(0.5, 0.5, Gravity::NORTH_EAST)
                ->build();

            expect($url)->toContain('crop:0.5:0.5:noea');
        });
    });
});
