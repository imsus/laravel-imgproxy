<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    // Enable short options for these tests (default behavior)
    config()->set('imgproxy.use_short_options', true);
});

describe('Short Options', function () {
    describe('use_short_options config', function () {
        beforeEach(function () {
            config()->set('imgproxy.use_short_options', false);
        });

        it('generates full option names when disabled', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200');
        });
    });

    describe('short option names when enabled', function () {
        beforeEach(function () {
            config()->set('imgproxy.use_short_options', true);
        });

        it('generates short option names when enabled', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->build();

            expect($url)->toContain('/w:300/h:200/');
        });

        it('generates short option for quality', function () {
            $url = imgproxy($this->sample_image_url)
                ->quality(85)
                ->build();

            expect($url)->toContain('/q:85/');
        });

        it('generates short option for dpr', function () {
            $url = imgproxy($this->sample_image_url)
                ->dpr(2)
                ->build();

            expect($url)->toContain('/dpr:2/');
        });

        it('generates short option for resizing type', function () {
            $url = imgproxy($this->sample_image_url)
                ->cover()
                ->build();

            expect($url)->toContain('/rt:fill');
        });
    });

    describe('short option mapping', function () {
        // Short options is enabled by default in tests

        it('maps width to w', function () {
            $url = imgproxy($this->sample_image_url)->width(300)->build();
            expect($url)->toContain('/w:300/');
        });

        it('maps height to h', function () {
            $url = imgproxy($this->sample_image_url)->height(200)->build();
            expect($url)->toContain('/h:200/');
        });

        it('maps quality to q', function () {
            $url = imgproxy($this->sample_image_url)->quality(85)->build();
            expect($url)->toContain('/q:85/');
        });

        it('maps dpr to dpr', function () {
            $url = imgproxy($this->sample_image_url)->dpr(2)->build();
            expect($url)->toContain('/dpr:2/');
        });

        it('maps resizing_type to rt', function () {
            $url = imgproxy($this->sample_image_url)->cover()->build();
            expect($url)->toContain('/rt:fill');
        });

        it('maps gravity to g', function () {
            $url = imgproxy($this->sample_image_url)
                ->setGravity(\Imsus\ImgProxy\Enums\Gravity::CENTER)
                ->build();
            expect($url)->toContain('/g:');
        });

        it('maps enlarge to el', function () {
            $url = imgproxy($this->sample_image_url)->enlarge()->build();
            expect($url)->toContain('/el:1');
        });
    });

    describe('multiple options with short names', function () {
        // Short options is enabled by default in tests

        it('combines multiple short options correctly', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->quality(85)
                ->dpr(2)
                ->build();

            expect($url)->toContain('/w:300/')
                ->and($url)->toContain('/h:200/')
                ->and($url)->toContain('/q:85/')
                ->and($url)->toContain('/dpr:2/');
        });
    });

    describe('converts short keys to full names when disabled', function () {
        beforeEach(function () {
            config()->set('imgproxy.use_short_options', false);
        });

        it('converts w to width', function () {
            $url = imgproxy($this->sample_image_url)->width(300)->build();
            expect($url)->toContain('/width:300/');
        });

        it('converts h to height', function () {
            $url = imgproxy($this->sample_image_url)->height(200)->build();
            expect($url)->toContain('/height:200/');
        });
    });
});
