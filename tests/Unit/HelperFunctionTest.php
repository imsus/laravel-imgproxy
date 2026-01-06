<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\ImgProxy;

describe('Helper Function', function () {
    it('can be called with a URL parameter', function () {
        $url = 'https://example.com/image.jpg';
        $instance = imgproxy($url);

        expect($instance)->toBeInstanceOf(ImgProxy::class);
        
        $result = $instance->setWidth(300)->build();
        expect($result)->toContain('width:300');
        expect($result)->toContain('example.com/image.jpg');
    });

    it('can be called without parameters', function () {
        $instance = imgproxy();

        expect($instance)->toBeInstanceOf(ImgProxy::class);
    });

    it('can chain url() method when called without parameters', function () {
        $url = 'https://example.com/image.jpg';
        $instance = imgproxy()->url($url);

        expect($instance)->toBeInstanceOf(ImgProxy::class);
        
        $result = $instance->setWidth(300)->build();
        expect($result)->toContain('width:300');
        expect($result)->toContain('example.com/image.jpg');
    });

    it('produces same result whether URL is passed or chained', function () {
        $url = 'https://example.com/image.jpg';
        
        $result1 = imgproxy($url)->setWidth(300)->setHeight(200)->build();
        $result2 = imgproxy()->url($url)->setWidth(300)->setHeight(200)->build();

        expect($result1)->toBe($result2);
    });

    it('can be called with null parameter', function () {
        $instance = imgproxy(null);

        expect($instance)->toBeInstanceOf(ImgProxy::class);
    });
});
