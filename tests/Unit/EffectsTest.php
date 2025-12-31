<?php

namespace Imsus\ImgProxy\Tests\Unit;

describe('Effects', function () {
    beforeEach(function () {
        $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
    });

    it('can use quality and effects in url building', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->setHeight(200)
            ->setQuality(85)
            ->setBlur(1.5)
            ->build();

        expect($url)->toContain('width:300');
        expect($url)->toContain('height:200');
        expect($url)->toContain('quality:85');
        expect($url)->toContain('blur:1.5');
    });
});
