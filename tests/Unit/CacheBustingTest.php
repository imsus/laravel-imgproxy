<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    // Enable short options for these tests
    config()->set('imgproxy.use_short_options', true);
});

describe('Cache Busting Methods', function () {
    describe('v()', function () {
        it('adds version to URL for cache busting (alias of cachebuster)', function () {
            $url = imgproxy($this->sample_image_url)
                ->v(1)
                ->build();

            expect($url)->toContain('cb:1');
        });

        it('accepts integer version', function () {
            $url = imgproxy($this->sample_image_url)
                ->v(12345)
                ->build();

            expect($url)->toContain('cb:12345');
        });

        it('accepts string version', function () {
            $url = imgproxy($this->sample_image_url)
                ->v('abc123')
                ->build();

            expect($url)->toContain('cb:abc123');
        });

        it('accepts semantic version string', function () {
            $url = imgproxy($this->sample_image_url)
                ->v('1.0.5')
                ->build();

            expect($url)->toContain('cb:1.0.5');
        });

        it('is chainable with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->v(2)
                ->webp()
                ->build();

            expect($url)->toContain('w:300')
                ->and($url)->toContain('h:200')
                ->and($url)->toContain('cb:2')
                ->and($url)->toContain('.webp');
        });

        it('works with multiple chained options', function () {
            $url = imgproxy($this->sample_image_url)
                ->width(300)
                ->height(200)
                ->cover()
                ->quality(85)
                ->webp()
                ->v('build-456')
                ->build();

            expect($url)->toContain('w:300')
                ->and($url)->toContain('h:200')
                ->and($url)->toContain('rt:fill')
                ->and($url)->toContain('q:85')
                ->and($url)->toContain('.webp')
                ->and($url)->toContain('cb:build-456');
        });

        it('can be used for cache invalidation', function () {
            $url1 = imgproxy($this->sample_image_url)->v(1)->build();
            $url2 = imgproxy($this->sample_image_url)->v(2)->build();

            expect($url1)->not->toBe($url2);
            expect($url1)->toContain('cb:1');
            expect($url2)->toContain('cb:2');
        });
    });

    describe('comparison with cachebuster()', function () {
        it('v() produces same result as cachebuster()', function () {
            $urlWithV = imgproxy($this->sample_image_url)->v('123')->build();
            $urlWithCachebuster = imgproxy($this->sample_image_url)->cachebuster('123')->build();

            expect($urlWithV)->toBe($urlWithCachebuster);
        });
    });
});
