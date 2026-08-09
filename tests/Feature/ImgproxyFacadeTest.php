<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Imgproxy;
use Imsus\LaravelImgproxy\Instance;
use Imsus\LaravelImgproxy\Manager;

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
        'presets' => [],
    ]);
});

it('builds a signed URL on the default instance through the facade', function () {
    expect(Imgproxy::url('http://example.com/image.jpg'))->toBeInstanceOf(Builder::class)
        ->and(Imgproxy::url('http://example.com/image.jpg')->url())
        ->toBe('https://imgproxy.example.com/-21kNUD97Cxp5oC7jAkCwnb4P6SgSaavMwg6PQVZzFU/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('keeps the fluent builder API through the facade', function () {
    expect(Imgproxy::url('http://example.com/image.jpg')->resize('fill', 300, 400)->format('webp')->url())
        ->toBe('https://imgproxy.example.com/VcfNyvwk0rTx_o3V22fw3xPC1oJuzD6dZSZMWNkomGs/rs:fill:300:400/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('builds a URL on a named instance with its own settings through the facade', function () {
    expect(Imgproxy::instance('staging'))->toBeInstanceOf(Instance::class)
        ->and(Imgproxy::instance('staging')->url('http://example.com/image.jpg')->url())
        ->toBe('https://imgproxy.staging.example.com/MpZcoK1O2EQ/plain/http%3A%2F%2Fexample.com%2Fimage.jpg');
});

it('throws when the instance is not configured', function () {
    Imgproxy::instance('missing');
})->throws(InvalidArgumentException::class);

it('exposes the manager through the imgproxy helper', function () {
    expect(imgproxy())->toBeInstanceOf(Manager::class)
        ->and(imgproxy())->toBe(app(Manager::class));
});

it('builds the same URL through the helper and the facade', function () {
    expect(imgproxy()->url('http://example.com/image.jpg')->url())
        ->toBe(Imgproxy::url('http://example.com/image.jpg')->url());
});
