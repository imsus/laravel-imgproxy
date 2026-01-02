<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/png';
});

describe('Format Shortcut Methods', function () {
    describe('webp()', function () {
        it('sets output format to WebP', function () {
            $url = imgproxy($this->sample_image_url)
                ->webp()
                ->build();

            expect($url)->toContain('.webp');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->webp()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('.webp');
        });
    });

    describe('avif()', function () {
        it('sets output format to AVIF', function () {
            $url = imgproxy($this->sample_image_url)
                ->avif()
                ->build();

            expect($url)->toContain('.avif');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->height(200)
                ->avif()
                ->build();

            expect($url)->toContain('height:200')
                ->and($url)->toContain('.avif');
        });
    });

    describe('png()', function () {
        it('sets output format to PNG', function () {
            $url = imgproxy($this->sample_image_url)
                ->png()
                ->build();

            expect($url)->toContain('.png');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->quality(90)
                ->png()
                ->build();

            expect($url)->toContain('quality:90')
                ->and($url)->toContain('.png');
        });
    });

    describe('jpg()', function () {
        it('sets output format to JPEG', function () {
            $url = imgproxy($this->sample_image_url)
                ->jpg()
                ->build();

            expect($url)->toContain('.jpg');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->dpr(2)
                ->jpg()
                ->build();

            expect($url)->toContain('dpr:2')
                ->and($url)->toContain('.jpg');
        });
    });

    describe('gif()', function () {
        it('sets output format to GIF', function () {
            $url = imgproxy($this->sample_image_url)
                ->gif()
                ->build();

            expect($url)->toContain('.gif');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->gif()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('.gif');
        });
    });

    describe('svg()', function () {
        it('sets output format to SVG', function () {
            $url = imgproxy($this->sample_image_url)
                ->svg()
                ->build();

            expect($url)->toContain('.svg');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->svg()
                ->build();

            expect($url)->toContain('width:300')
                ->and($url)->toContain('.svg');
        });
    });

    describe('combined usage', function () {
        it('can use multiple format shortcuts in chain', function () {
            $url1 = imgproxy($this->sample_image_url)->webp()->build();
            $url2 = imgproxy($this->sample_image_url)->avif()->build();
            $url3 = imgproxy($this->sample_image_url)->png()->build();
            $url4 = imgproxy($this->sample_image_url)->jpg()->build();
            $url5 = imgproxy($this->sample_image_url)->gif()->build();
            $url6 = imgproxy($this->sample_image_url)->svg()->build();

            expect($url1)->toContain('.webp');
            expect($url2)->toContain('.avif');
            expect($url3)->toContain('.png');
            expect($url4)->toContain('.jpg');
            expect($url5)->toContain('.gif');
            expect($url6)->toContain('.svg');
        });

        it('last format shortcut wins', function () {
            $url = imgproxy($this->sample_image_url)
                ->webp()
                ->png()
                ->jpg()
                ->build();

            expect($url)->toContain('.jpg')
                ->and($url)->not->toContain('.webp')
                ->and($url)->not->toContain('.png');
        });

        it('produces same result as setExtension', function () {
            $webpShortcut = imgproxy($this->sample_image_url)->webp()->build();
            $webpSetter = imgproxy($this->sample_image_url)->setExtension(OutputExtension::WEBP)->build();

            expect($webpShortcut)->toBe($webpSetter);
        });
    });
});
