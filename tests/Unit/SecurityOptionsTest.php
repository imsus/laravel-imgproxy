<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Security Options', function () {
    describe('setMaxSrcResolution', function () {
        it('sets max source resolution', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxSrcResolution(50)
                ->build();

            expect($url)->toContain('msr:50');
        });

        it('accepts float values', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxSrcResolution(12.5)
                ->build();

            expect($url)->toContain('msr:12.5');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxSrcResolution(100);

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('setMaxSrcFileSize', function () {
        it('sets max source file size in bytes', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxSrcFileSize(10485760)
                ->build();

            expect($url)->toContain('msfs:10485760');
        });

        it('accepts large file sizes', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxSrcFileSize(104857600)
                ->build();

            expect($url)->toContain('msfs:104857600');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxSrcFileSize(10485760);

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('setMaxAnimationFrames', function () {
        it('sets max animation frames', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxAnimationFrames(100)
                ->build();

            expect($url)->toContain('maf:100');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxAnimationFrames(100);

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('setMaxAnimationFrameResolution', function () {
        it('sets max animation frame resolution', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxAnimationFrameResolution(2000)
                ->build();

            expect($url)->toContain('mafr:2000');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxAnimationFrameResolution(2000);

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('setMaxResultDimension', function () {
        it('sets max result dimension', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxResultDimension(4096)
                ->build();

            expect($url)->toContain('mrd:4096');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxResultDimension(4096);

            expect($result)->toBe($this->imgProxy);
        });
    });
});
