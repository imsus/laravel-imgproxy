<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    // Enable short options for these tests
    config()->set('imgproxy.use_short_options', true);
});

describe('Processing Options', function () {
    describe('skipProcessing', function () {
        it('skips processing for formats', function () {
            $url = imgproxy($this->sample_image_url)
                ->skipProcessing(['png', 'gif'])
                ->build();

            expect($url)->toContain('skp:png:gif');
        });

        it('skips processing for single format', function () {
            $url = imgproxy($this->sample_image_url)
                ->skipProcessing('png')
                ->build();

            expect($url)->toContain('skp:png');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->skipProcessing('png');

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('raw', function () {
        it('enables raw by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->raw()
                ->build();

            expect($url)->toContain('raw:1');
        });

        it('can disable raw', function () {
            $url = imgproxy($this->sample_image_url)
                ->raw(false)
                ->build();

            expect($url)->toContain('raw:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->raw();

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('cachebuster', function () {
        it('sets cachebuster value', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->cachebuster('v1.2.3')
                ->build();

            expect($url)->toContain('cb:v1.2.3');
        });

        it('works with other options', function () {
            $url = imgproxy($this->sample_image_url)
                ->setWidth(300)
                ->cachebuster('random')
                ->build();

            expect($url)->toContain('cb:random')
                ->and($url)->toContain('w:300');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->cachebuster('v1');

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('expires', function () {
        it('sets expiration timestamp', function () {
            $url = imgproxy($this->sample_image_url)
                ->expires(1704067200)
                ->build();

            expect($url)->toContain('exp:1704067200');
        });

        it('accepts future timestamp', function () {
            $url = imgproxy($this->sample_image_url)
                ->expires(time() + 3600)
                ->build();

            expect($url)->toContain('exp:');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->expires(time() + 3600);

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('filename', function () {
        it('sets filename for content disposition', function () {
            $url = imgproxy($this->sample_image_url)
                ->filename('my-image')
                ->build();

            expect($url)->toContain('fn:my-image');
        });

        it('supports encoded filename', function () {
            $url = imgproxy($this->sample_image_url)
                ->filename('my-image.jpg', true)
                ->build();

            expect($url)->toContain('fn:bXktaW1hZ2UuanBn:1');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->filename('test');

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('returnAttachment', function () {
        it('enables return attachment by default', function () {
            $url = imgproxy($this->sample_image_url)
                ->returnAttachment()
                ->build();

            expect($url)->toContain('att:1');
        });

        it('can disable return attachment', function () {
            $url = imgproxy($this->sample_image_url)
                ->returnAttachment(false)
                ->build();

            expect($url)->toContain('att:0');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->returnAttachment();

            expect($result)->toBe($this->imgProxy);
        });
    });

    describe('preset', function () {
        it('uses single preset', function () {
            $url = imgproxy($this->sample_image_url)
                ->preset('thumbnail')
                ->build();

            expect($url)->toContain('pr:thumbnail');
        });

        it('uses multiple presets', function () {
            $url = imgproxy($this->sample_image_url)
                ->preset(['thumbnail', 'blurry'])
                ->build();

            expect($url)->toContain('pr:thumbnail:blurry');
        });

        it('returns self for method chaining', function () {
            $result = $this->imgProxy->preset('test');

            expect($result)->toBe($this->imgProxy);
        });
    });
});
