<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\SourceUrlMode;

beforeEach(function () {
    $this->sample_image_url = 'https://placehold.co/600x400/jpeg';
});

describe('UrlGeneration', function () {
    it('can generate signed url', function () {
        $url = imgproxy($this->sample_image_url)
            ->setProcessing('rs:fit:300:200:0/g:no')
            ->setExtension(OutputExtension::JPEG)
            ->build();

        expect($url)->toBe('http://localhost:8080/xyeeqF4mNUiHYF5afTDuCmDUuI0VcfCBbkEX3ig3-bo/rs:fit:300:200:0/g:no/aHR0cHM6Ly9wbGFjZWhvbGQuY28vNjAweDQwMC9qcGVn.jpg');
    });

    it('can generate signed url in plain mode', function () {
        $url = imgproxy($this->sample_image_url)
            ->setProcessing('rs:fit:300:200:0/g:no')
            ->setExtension(OutputExtension::JPEG)
            ->setMode(SourceUrlMode::PLAIN)
            ->build();

        expect($url)->toBe("http://localhost:8080/Z9XGOwpdt7sQVI6k-5s1JHX7XS7xVBuWja-T94UktN4/rs:fit:300:200:0/g:no/plain/{$this->sample_image_url}@jpg");
    });

    it('can generate insecure url', function () {
        config()->set('imgproxy.key', '');
        config()->set('imgproxy.salt', '');

        $url = imgproxy($this->sample_image_url)
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

    it('returns original url for invalid urls', function () {
        $invalidUrl = 'not-a-valid-url';
        $url = imgproxy($invalidUrl)
            ->setWidth(100)
            ->build();

        expect($url)->toBe($invalidUrl);
    });

    it('returns original url for empty urls', function () {
        $emptyUrl = '';
        $url = imgproxy($emptyUrl)
            ->setWidth(100)
            ->build();

        expect($url)->toBe($emptyUrl);
    });
});
