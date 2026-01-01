<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->imgProxy = new ImgProxy;
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('JPEG XL Format', function () {
    it('sets JPEG XL extension', function () {
        $url = imgproxy($this->sample_image_url)
            ->setExtension(OutputExtension::JPEG_XL)
            ->build();

        expect($url)->toContain('.jxl');
    });

    it('can generate URL with JPEG XL', function () {
        $url = imgproxy($this->sample_image_url)
            ->setWidth(300)
            ->setExtension(OutputExtension::JPEG_XL)
            ->build();

        expect($url)->toContain('width:300')
            ->and($url)->toContain('.jxl');
    });

    it('works with quality settings', function () {
        $url = imgproxy($this->sample_image_url)
            ->setExtension(OutputExtension::JPEG_XL)
            ->setQuality(85)
            ->build();

        expect($url)->toContain('.jxl')
            ->and($url)->toContain('quality:85');
    });

    it('works with format quality settings', function () {
        $url = imgproxy($this->sample_image_url)
            ->setExtension(OutputExtension::JPEG_XL)
            ->setFormatQuality('jxl', 90)
            ->build();

        expect($url)->toContain('.jxl')
            ->and($url)->toContain('fq:jxl:90');
    });
});
