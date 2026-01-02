<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Fit Shortcut Methods', function () {
    describe('cover()', function () {
        it('sets resize type to FILL (cover)', function () {
            $url = imgproxy($this->sample_image_url)
                ->cover()
                ->build();

            expect($url)->toContain('resizing_type:fill');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->cover()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('resizing_type:fill');
        });
    });

    describe('contain()', function () {
        it('sets resize type to FIT (contain)', function () {
            $url = imgproxy($this->sample_image_url)
                ->contain()
                ->build();

            expect($url)->toContain('resizing_type:fit');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->contain()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('resizing_type:fit');
        });
    });

    describe('fill()', function () {
        it('sets resize type to FILL_DOWN (fill)', function () {
            $url = imgproxy($this->sample_image_url)
                ->fill()
                ->build();

            expect($url)->toContain('resizing_type:fill-down');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->fill()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('resizing_type:fill-down');
        });
    });

    describe('combined usage', function () {
        it('can chain fit shortcuts', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->cover()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('resizing_type:fill');
        });

        it('last fit shortcut wins', function () {
            $url = imgproxy($this->sample_image_url)
                ->cover()
                ->contain()
                ->fill()
                ->build();

            expect($url)->toContain('resizing_type:fill-down')
                ->and($url)->not->toContain('resizing_type:fill/')
                ->and($url)->not->toContain('resizing_type:fit/');
        });

        it('produces same result as setResizeType', function () {
            $coverShortcut = imgproxy($this->sample_image_url)->cover()->build();
            $coverSetter = imgproxy($this->sample_image_url)->setResizeType(ResizeType::FILL)->build();
            expect($coverShortcut)->toBe($coverSetter);

            $containShortcut = imgproxy($this->sample_image_url)->contain()->build();
            $containSetter = imgproxy($this->sample_image_url)->setResizeType(ResizeType::FIT)->build();
            expect($containShortcut)->toBe($containSetter);

            $fillShortcut = imgproxy($this->sample_image_url)->fill()->build();
            $fillSetter = imgproxy($this->sample_image_url)->setResizeType(ResizeType::FILL_DOWN)->build();
            expect($fillShortcut)->toBe($fillSetter);
        });
    });

    describe('with quality and format shortcuts', function () {
        it('works with all fluent API methods', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->cover()
                ->quality(85)
                ->webp()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('height:200')
                ->and($url)->toContain('resizing_type:fill')
                ->and($url)->toContain('quality:85')
                ->and($url)->toContain('.webp');
        });
    });
});
