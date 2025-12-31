<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\ResizeType;

describe('S3Url', function () {
    it('accepts s3:// source urls', function () {
        $s3Url = 's3://files.yoomarket/path/to/image.webp';
        $url = imgproxy($s3Url)
            ->setWidth(432)
            ->build();

        expect($url)->not->toBe($s3Url);
        expect($url)->toContain(rtrim(strtr(base64_encode($s3Url), '+/', '-_'), '='));
        expect($url)->toContain('width:432');
    });

    it('can build s3 url with processing options', function () {
        $s3Url = 's3://bucket/path/image.jpg';
        $url = imgproxy($s3Url)
            ->setWidth(100)
            ->setHeight(100)
            ->setResizeType(ResizeType::FIT)
            ->build();

        expect($url)->toContain('width:100');
        expect($url)->toContain('height:100');
        expect($url)->toContain('resizing_type:fit');
        expect($url)->toContain(rtrim(strtr(base64_encode($s3Url), '+/', '-_'), '='));
    });
});
