<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Imgproxy;

beforeEach(function () {
    config()->set('laravel-imgproxy', [
        'default' => 'default',
        'instances' => [
            'default' => [
                'url' => 'https://imgproxy.example.com',
                'key' => 'a1b2c3d4',
                'salt' => 'e5f60718',
                'signature_size' => null,
                'encoding' => 'base64',
            ],
            'staging' => [
                'url' => 'https://imgproxy.staging.example.com',
                'key' => 'a1b2c3d4',
                'salt' => 'e5f60718',
                'signature_size' => 8,
                'encoding' => 'plain',
            ],
        ],
        'presets' => [
            'thumb' => ['resize' => 'fill', 'width' => 300, 'height' => 300],
        ],
    ]);
});

it('composes a configured preset on the default instance', function () {
    expect(Imgproxy::url('http://example.com/image.jpg')->preset('thumb')->url())
        ->toBe('https://imgproxy.example.com/Q4o_jv7N4mdlFZOZ2RCk-c-5bmm40pVxE40qYzOwfiU/rs:fill/w:300/h:300/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('composes the same preset on a named instance with its own encoding and signature size', function () {
    expect(Imgproxy::instance('staging')->url('http://example.com/image.jpg')->preset('thumb')->url())
        ->toBe('https://imgproxy.staging.example.com/WCHswduQdvM/rs:fill/w:300/h:300/plain/http%3A%2F%2Fexample.com%2Fimage.jpg');
});

it('builds a placeholder on a named instance', function () {
    expect(Imgproxy::instance('staging')->url('http://example.com/image.jpg')->placeholder()->url())
        ->toBe('https://imgproxy.staging.example.com/vvgI0uBj6kk/w:16/bl:8/f:webp/plain/http%3A%2F%2Fexample.com%2Fimage.jpg');
});

it('throws on an unconfigured preset through the facade', function () {
    Imgproxy::url('http://example.com/image.jpg')->preset('missing');
})->throws(InvalidArgumentException::class, 'preset [missing] is not configured');
