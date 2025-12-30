<?php

namespace Imsus\ImgProxy\Tests;

use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\SourceUrlMode;
use Imsus\ImgProxy\ImgProxy;

beforeEach(function () {
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

it('can generate signed url', function () {
    $imgProxy = new ImgProxy;
    $url = $imgProxy
        ->url($this->sample_image_url)
        ->setProcessing('rs:fit:300:200:0/g:no')
        ->setExtension(OutputExtension::JPEG)
        ->build();

    expect($url)->toBe('http://localhost:8080/xyeeqF4mNUiHYF5afTDuCmDUuI0VcfCBbkEX3ig3-bo/rs:fit:300:200:0/g:no/aHR0cHM6Ly9wbGFjZWhvbGQuY28vNjAweDQwMC9qcGVn.jpg');
});

it('can generate signed url in plain mode', function () {
    $imgProxy = new ImgProxy;
    $url = $imgProxy
        ->url($this->sample_image_url)
        ->setProcessing('rs:fit:300:200:0/g:no')
        ->setExtension(OutputExtension::JPEG)
        ->setMode(SourceUrlMode::PLAIN)
        ->build();

    expect($url)->toBe("http://localhost:8080/Z9XGOwpdt7sQVI6k-5s1JHX7XS7xVBuWja-T94UktN4/rs:fit:300:200:0/g:no/plain/{$this->sample_image_url}@jpg");
});

it('can generate insecure url', function () {
    config()->set('imgproxy.key', '');
    config()->set('imgproxy.salt', '');

    $imgProxy = new ImgProxy;
    $url = $imgProxy
        ->url($this->sample_image_url)
        ->setProcessing('rs:fit:300:200:0/g:no')
        ->setExtension(OutputExtension::JPEG)
        ->setMode(SourceUrlMode::PLAIN)
        ->build();

    expect($url)->toBe('http://localhost:8080/insecure/rs:fit:300:200:0/g:no/plain/https://placehold.co/600x400/jpeg@jpg');
});

it('can use helper function', function () {
    $url = imgproxy($this->sample_image_url)
        ->setProcessing('rs:fit:300:200:0/g:no')
        ->build();

    expect($url)->toBe('http://localhost:8080/xyeeqF4mNUiHYF5afTDuCmDUuI0VcfCBbkEX3ig3-bo/rs:fit:300:200:0/g:no/aHR0cHM6Ly9wbGFjZWhvbGQuY28vNjAweDQwMC9qcGVn.jpg');
});

it('can use fluent methods', function () {
    $url = imgproxy($this->sample_image_url)
        ->setWidth(100)
        ->setHeight(100)
        ->build();

    expect($url)->toBe('http://localhost:8080/Bi1ABm01lyP2ReV49nRXSqwIP8dRzH_MYW_sb2GttMU/width:100/height:100/aHR0cHM6Ly9wbGFjZWhvbGQuY28vNjAweDQwMC9qcGVn.jpg');
});

// Edge case tests
it('returns original url for invalid urls', function () {
    $invalidUrl = 'not-a-valid-url';
    $imgProxy = new ImgProxy;
    $url = $imgProxy
        ->url($invalidUrl)
        ->setWidth(100)
        ->build();

    expect($url)->toBe($invalidUrl);
});

it('returns original url for empty urls', function () {
    $emptyUrl = '';
    $imgProxy = new ImgProxy;
    $url = $imgProxy
        ->url($emptyUrl)
        ->setWidth(100)
        ->build();

    expect($url)->toBe($emptyUrl);
});

it('throws exception for invalid hex key', function () {
    expect(function () {
        config()->set('imgproxy.key', 'invalid-hex');
        new ImgProxy;
    })->toThrow(\InvalidArgumentException::class, 'The key must be a hex-encoded string.');
});

it('throws exception for invalid hex salt', function () {
    expect(function () {
        config()->set('imgproxy.salt', 'invalid-hex');
        new ImgProxy;
    })->toThrow(\InvalidArgumentException::class, 'The salt must be a hex-encoded string.');
});

it('validates dpr bounds', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setDpr(0);
    })->toThrow(\InvalidArgumentException::class, 'DPR (Device Pixel Ratio) must be between 1 and 8');

    expect(function () use ($imgProxy) {
        $imgProxy->setDpr(9);
    })->toThrow(\InvalidArgumentException::class, 'DPR (Device Pixel Ratio) must be between 1 and 8');
});

it('accepts valid dpr values', function () {
    $imgProxy = new ImgProxy;

    // Should not throw exceptions
    $imgProxy->setDpr(1);
    $imgProxy->setDpr(4);
    $imgProxy->setDpr(8);

    expect(true)->toBe(true);
});

// Quality and effects tests
it('validates quality bounds', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setQuality(-1);
    })->toThrow(\InvalidArgumentException::class, 'Quality must be between 0 and 100');

    expect(function () use ($imgProxy) {
        $imgProxy->setQuality(101);
    })->toThrow(\InvalidArgumentException::class, 'Quality must be between 0 and 100');
});

it('accepts valid quality values', function () {
    $imgProxy = new ImgProxy;

    $imgProxy->setQuality(0);
    $imgProxy->setQuality(50);
    $imgProxy->setQuality(100);

    expect(true)->toBe(true);
});

it('validates blur sigma values', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setBlur(-1.0);
    })->toThrow(\InvalidArgumentException::class, 'Blur sigma must be 0.0 or greater');
});

it('validates sharpen sigma values', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setSharpen(-1.0);
    })->toThrow(\InvalidArgumentException::class, 'Sharpen sigma must be 0.0 or greater');
});

it('validates brightness bounds', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setBrightness(-256);
    })->toThrow(\InvalidArgumentException::class, 'Brightness must be between -255 and 255');

    expect(function () use ($imgProxy) {
        $imgProxy->setBrightness(256);
    })->toThrow(\InvalidArgumentException::class, 'Brightness must be between -255 and 255');
});

it('validates contrast values', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setContrast(-1.0);
    })->toThrow(\InvalidArgumentException::class, 'Contrast must be 0.0 or greater');
});

it('validates saturation values', function () {
    $imgProxy = new ImgProxy;

    expect(function () use ($imgProxy) {
        $imgProxy->setSaturation(-1.0);
    })->toThrow(\InvalidArgumentException::class, 'Saturation must be 0.0 or greater');
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

// S3 URL tests
it('accepts s3:// source urls', function () {
    $s3Url = 's3://files.yoomarket/path/to/image.webp';
    $url = imgproxy($s3Url)
        ->setWidth(432)
        ->build();

    expect($url)->not->toBe($s3Url);
    expect($url)->toContain(base64_encode($s3Url));
    expect($url)->toContain('width:432');
});

it('can build s3 url with processing options', function () {
    $s3Url = 's3://bucket/path/image.jpg';
    $url = imgproxy($s3Url)
        ->setWidth(100)
        ->setHeight(100)
        ->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FIT)
        ->build();

    expect($url)->toContain('width:100');
    expect($url)->toContain('height:100');
    expect($url)->toContain('resizing_type:fit');
    expect($url)->toContain(rtrim(strtr(base64_encode($s3Url), '+/', '-_'), '='));
});
