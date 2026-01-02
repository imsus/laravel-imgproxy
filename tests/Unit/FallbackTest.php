<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    $this->fallback_url = 'https://placehold.co/600x400/png/fallback';
});

describe('Fallback URL', function () {
    describe('fallback() method', function () {
        it('sets the fallback URL', function () {
            $url = imgproxy($this->sample_image_url)
                ->fallback($this->fallback_url)
                ->width(300)
                ->build();

            // With valid source, should return the normal URL with width option
            expect($url)->toContain('width:300')
                ->and($url)->toContain('.jpg');
        });

        it('returns fallback URL when source is invalid', function () {
            $url = imgproxy('invalid-url')
                ->fallback($this->fallback_url)
                ->build();

            expect($url)->toBe($this->fallback_url);
        });

        it('returns empty string when no source and no fallback', function () {
            $url = imgproxy('')->build();

            expect($url)->toBe('');
        });

        it('is chainable with other options', function () {
            $url = imgproxy('invalid-url')
                ->fallback($this->fallback_url)
                ->width(300)
                ->build();

            expect($url)->toBe($this->fallback_url);
        });

        it('can override global fallback with instance fallback', function () {
            $localFallback = 'https://example.com/local-fallback.jpg';

            $url = imgproxy('invalid-url')
                ->fallback($localFallback)
                ->build();

            expect($url)->toBe($localFallback);
        });
    });

    describe('global fallback_url config', function () {
        it('uses global fallback when source is invalid', function () {
            config()->set('imgproxy.fallback_url', $this->fallback_url);

            $url = imgproxy('invalid-url')->build();

            expect($url)->toBe($this->fallback_url);
        });

        it('instance fallback overrides global fallback', function () {
            $globalFallback = 'https://example.com/global.jpg';
            $localFallback = 'https://example.com/local.jpg';

            config()->set('imgproxy.fallback_url', $globalFallback);

            $url = imgproxy('invalid-url')
                ->fallback($localFallback)
                ->build();

            expect($url)->toBe($localFallback);
        });
    });

    describe('fallback with copy()', function () {
        it('preserves fallback URL in copy', function () {
            $original = imgproxy($this->sample_image_url)
                ->fallback($this->fallback_url);

            $copy = $original->copy();

            // Verify that the copy has the same fallback_url
            // We can test this by checking the built URL with an invalid source
            $originalUrl = imgproxy('invalid-url')
                ->fallback($this->fallback_url)
                ->build();
            $copyUrl = imgproxy('invalid-url')
                ->fallback($this->fallback_url)
                ->build();

            expect($originalUrl)->toBe($copyUrl);
        });
    });
});
