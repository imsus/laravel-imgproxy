<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('API Alias Methods', function () {
    describe('width()', function () {
        it('sets width as alias of setWidth', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->build();

            expect($url)->toContain('width:300');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200');
        });

        it('is chainable', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->quality(85)
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('quality:85');
        });
    });

    describe('height()', function () {
        it('sets height as alias of setHeight', function () {
            $url = imgproxy($this->sample_image_url)
                ->height(200)
                ->build();

            expect($url)->toContain('height:200');
        });
    });

    describe('quality()', function () {
        it('sets quality as alias of setQuality', function () {
            $url = imgproxy($this->sample_image_url)
                ->quality(85)
                ->build();

            expect($url)->toContain('quality:85');
        });

        it('throws exception for quality out of range', function () {
            expect(fn () => imgproxy($this->sample_image_url)->quality(150))
                ->toThrow(\InvalidArgumentException::class, 'Quality must be between 0 and 100');
        });

        it('accepts valid quality values', function () {
            $url = imgproxy($this->sample_image_url)->quality(0)->build();
            expect($url)->toContain('quality:0');

            $url = imgproxy($this->sample_image_url)->quality(50)->build();
            expect($url)->toContain('quality:50');

            $url = imgproxy($this->sample_image_url)->quality(100)->build();
            expect($url)->toContain('quality:100');
        });
    });

    describe('dpr()', function () {
        it('sets dpr as alias of setDpr', function () {
            $url = imgproxy($this->sample_image_url)
                ->dpr(2)
                ->build();

            expect($url)->toContain('dpr:2');
        });

        it('throws exception for dpr out of range', function () {
            expect(fn () => imgproxy($this->sample_image_url)->dpr(10))
                ->toThrow(\InvalidArgumentException::class, 'DPR (Device Pixel Ratio) must be between 1 and 8');
        });

        it('accepts valid dpr values', function () {
            $url = imgproxy($this->sample_image_url)->dpr(1)->build();
            expect($url)->toContain('dpr:1');

            $url = imgproxy($this->sample_image_url)->dpr(8)->build();
            expect($url)->toContain('dpr:8');
        });
    });

    describe('fluent API usage', function () {
        it('works with new fluent API', function () {
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

        it('produces same result as set* methods', function () {
            $fluentUrl = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->quality(85)
                ->build();

            $setterUrl = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setHeight(200)
                ->setQuality(85)
                ->build();

            expect($fluentUrl)->toBe($setterUrl);
        });
    });
});
