<?php

namespace Imsus\ImgProxy\Tests\Unit;

describe('Clone Method', function () {
    beforeEach(function () {
        $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    });

    it('creates a copy with the same URL', function () {
        $original = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->setHeight(200);

        $copy = $original->copy();

        // Verify the copy has the same source URL
        $copyUrl = $copy->build();

        expect($copyUrl)->toContain('width:300')
            ->and($copyUrl)->toContain('height:200');
    });

    it('modifications to copy do not affect original', function () {
        $original = imgproxy($this->sample_image_url)
            ->setWidth(300);

        $copy = $original->copy();
        $copy->setWidth(500);

        // Original should still have width 300
        $originalUrl = $original->build();
        expect($originalUrl)->toContain('width:300');

        // Copy should have width 500
        $copyUrl = $copy->build();
        expect($copyUrl)->toContain('width:500');
    });

    it('copies all options', function () {
        $original = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->setHeight(200)
            ->setQuality(85)
            ->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL);

        $copy = $original->copy();
        $copyUrl = $copy->build();

        expect($copyUrl)->toContain('width:300')
            ->and($copyUrl)->toContain('height:200')
            ->and($copyUrl)->toContain('quality:85')
            ->and($copyUrl)->toContain('resizing_type:fill');
    });

    it('can chain methods after copying', function () {
        $original = imgproxy($this->sample_image_url)
            ->setWidth(300);

        $copy = $original->copy()
            ->setHeight(200)
            ->setQuality(90);

        $copyUrl = $copy->build();

        expect($copyUrl)->toContain('width:300')
            ->and($copyUrl)->toContain('height:200')
            ->and($copyUrl)->toContain('quality:90');
    });

    it('creates independent copies', function () {
        $original = imgproxy($this->sample_image_url)
            ->setWidth(300);

        $copy1 = $original->copy();
        $copy2 = $original->copy();

        $copy1->setWidth(400);
        $copy2->setWidth(500);

        expect($original->build())->toContain('width:300');
        expect($copy1->build())->toContain('width:400');
        expect($copy2->build())->toContain('width:500');
    });

    it('works with empty options', function () {
        $original = imgproxy($this->sample_image_url);
        $copy = $original->copy();

        expect($copy->build())->toBeString();
    });
});
