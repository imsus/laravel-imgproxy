<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('Quality Options', function () {
    describe('setFormatQuality', function () {
        it('sets quality for single format', function () {
            $url = imgproxy($this->sample_image_url)
                ->setFormatQuality('jpg', 80)
                ->build();

            expect($url)->toContain('fq:jpg:80');
        });

        it('sets quality for multiple formats', function () {
            $url = imgproxy($this->sample_image_url)
                ->setFormatQuality(['jpg' => 80, 'webp' => 90])
                ->build();

            expect($url)->toContain('fq:jpg:80:webp:90');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->setFormatQuality('jpg', 85)
                ->build();

            expect($url)->toContain('fq:jpg:85')
                ->and($url)->toContain('width:300');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setFormatQuality('jpg', 80);

            expect($result)->toBe($this->imgProxy);
        });

        it('accepts raw format quality string', function () {
            $url = imgproxy($this->sample_image_url)
                ->setFormatQuality('raw:jpg:90')
                ->build();

            expect($url)->toContain('fq:raw:jpg:90');
        });
    });

    describe('setMaxBytes', function () {
        it('sets max bytes', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxBytes(102400)
                ->build();

            expect($url)->toContain('mb:102400');
        });

        it('accepts large values', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxBytes(1048576)
                ->build();

            expect($url)->toContain('mb:1048576');
        });

        it('validates value is non-negative', function () {
            expect(function () {
                $this->imgProxy->setMaxBytes(-1);
            })->toThrow(\InvalidArgumentException::class, 'Max bytes must be 0 or greater');
        });

        it('accepts zero (no limit)', function () {
            $url = imgproxy($this->sample_image_url)
                ->setMaxBytes(0)
                ->build();

            expect($url)->toContain('mb:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->setMaxBytes(102400);

            expect($result)->toBe($this->imgProxy);
        });
    });
});
