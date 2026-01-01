<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Metadata Options', function () {
    describe('keepCopyright', function () {
        it('enables keep copyright by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->keepCopyright()
                ->build();

            expect($url)->toContain('kcr:1');
        });

        it('can disable keep copyright', function () {
            $url = imgproxy($this->sample_image_url)
                ->keepCopyright(false)
                ->build();

            expect($url)->toContain('kcr:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->keepCopyright();

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('stripColorProfile', function () {
        it('enables strip color profile by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->stripColorProfile()
                ->build();

            expect($url)->toContain('scp:1');
        });

        it('can disable strip color profile', function () {
            $url = imgproxy($this->sample_image_url)
                ->stripColorProfile(false)
                ->build();

            expect($url)->toContain('scp:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->stripColorProfile();

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('enforceThumbnail', function () {
        it('enables enforce thumbnail by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->enforceThumbnail()
                ->build();

            expect($url)->toContain('eth:1');
        });

        it('can disable enforce thumbnail', function () {
            $url = imgproxy($this->sample_image_url)
                ->enforceThumbnail(false)
                ->build();

            expect($url)->toContain('eth:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->enforceThumbnail();

            expect($result)->toBe($this->imgProxy);
        });
    });
});
