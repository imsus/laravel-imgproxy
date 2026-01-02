<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    // Enable short options for these tests
    config()->set('imgproxy.use_short_options', true);
});

describe('Resize Options', function () {
    describe('enlarge', function () {
        it('enables enlarge by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->enlarge()
                ->build();

            expect($url)->toContain('el:1');
        });

        it('can disable enlarge', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->enlarge(false)
                ->build();

            expect($url)->toContain('el:0');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->enlarge()
                ->setQuality(85)
                ->build();

            expect($url)->toContain('el:1')
                ->and($url)->toContain('w:300')
                ->and($url)->toContain('q:85');
        });
    });

    describe('extend', function () {
        it('enables extend with default center gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->extend()
                ->build();

            expect($url)->toContain('ex:1:ce:0:0');
        });

        it('enables extend with custom gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->extend(Gravity::NORTH_EAST)
                ->build();

            expect($url)->toContain('ex:1:noea:0:0');
        });

        it('can disable extend', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->extend(false)
                ->build();

            expect($url)->toContain('ex:0');
        });
    });

    describe('extendAspectRatio', function () {
        it('enables extend with default center gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->extendAspectRatio()
                ->build();

            expect($url)->toContain('exar:1:ce:0:0');
        });

        it('enables extend with custom gravity', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->extendAspectRatio(Gravity::SOUTH_WEST)
                ->build();

            expect($url)->toContain('exar:1:sowe:0:0');
        });
    });

    describe('setExtendAspectRatio', function () {
        it('enables extend aspect ratio', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->setExtendAspectRatio(true)
                ->build();

            expect($url)->toContain('exar:1:ce:0:0');
        });

        it('disables extend aspect ratio', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->setExtendAspectRatio(false)
                ->build();

            expect($url)->toContain('exar:0');
        });
    });

    describe('minWidth', function () {
        it('sets minimum width', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->minWidth(100)
                ->build();

            expect($url)->toContain('mw:100');
        });

        it('accepts zero (no minimum)', function () {
            $url = imgproxy($this->sample_image_url)
                ->minWidth(0)
                ->build();

            expect($url)->toContain('mw:0');
        });
    });

    describe('minHeight', function () {
        it('sets minimum height', function () {
            $url = imgproxy($this->sample_image_url)
                ->setHeight(300)
                ->minHeight(100)
                ->build();

            expect($url)->toContain('mh:100');
        });

        it('accepts zero (no minimum)', function () {
            $url = imgproxy($this->sample_image_url)
                ->minHeight(0)
                ->build();

            expect($url)->toContain('mh:0');
        });
    });
});
