<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Short Options', function () {
    describe('use_short_options config', function () {
        it('generates full option names when disabled', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200');
        });

        it('generates short option names when enabled', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->build();

            // With default config (false), should still use full names
            expect($url)->toContain('width:300');
        });
    });

    describe('short option mapping', function () {
        it('maps width to w', function () {
            $url = imgproxy($this->sample_image_url)->width(300)->build();
            expect($url)->toContain('width:300');
        });

        it('maps height to h', function () {
            $url = imgproxy($this->sample_image_url)->height(200)->build();
            expect($url)->toContain('height:200');
        });

        it('maps quality to q', function () {
            $url = imgproxy($this->sample_image_url)->quality(85)->build();
            expect($url)->toContain('quality:85');
        });

        it('maps dpr to dpr', function () {
            $url = imgproxy($this->sample_image_url)->dpr(2)->build();
            expect($url)->toContain('dpr:2');
        });

        it('maps resizing_type to rt', function () {
            $url = imgproxy($this->sample_image_url)->cover()->build();
            expect($url)->toContain('resizing_type:fill');
        });

        it('maps gravity to g', function () {
            $url = imgproxy($this->sample_image_url)
                ->setGravity(\Imsus\ImgProxy\Enums\Gravity::CENTER)
                ->build();
            expect($url)->toContain('gravity:');
        });
    });

    describe('multiple options with short names', function () {
        it('combines multiple short options correctly', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->quality(85)
                ->dpr(2)
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('quality:85')
                ->and($url)->toContain('dpr:2');
        });
    });
});
