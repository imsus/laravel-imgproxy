<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Instance;
use Imsus\LaravelImgproxy\Manager;

beforeEach(function () {
    $this->config = [
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
    ];
});

it('returns the default instance name', function () {
    $manager = new Manager($this->config);

    expect($manager->defaultInstance())->toBe('default');
});

it('resolves the default instance when no name is given', function () {
    $manager = new Manager($this->config);

    $instance = $manager->instance();

    expect($instance)->toBeInstanceOf(Instance::class)
        ->and($instance->baseUrl())->toBe('https://imgproxy.example.com')
        ->and($instance->key())->toBe('a1b2c3d4')
        ->and($instance->salt())->toBe('e5f60718')
        ->and($instance->signatureSize())->toBeNull()
        ->and($instance->encoding())->toBe('base64');
});

it('resolves a named instance with its own settings', function () {
    $manager = new Manager($this->config);

    $instance = $manager->instance('staging');

    expect($instance)->toBeInstanceOf(Instance::class)
        ->and($instance->baseUrl())->toBe('https://imgproxy.staging.example.com')
        ->and($instance->key())->toBe('a1b2c3d4')
        ->and($instance->salt())->toBe('e5f60718')
        ->and($instance->signatureSize())->toBe(8)
        ->and($instance->encoding())->toBe('plain');
});

it('throws when the instance is not configured', function () {
    $manager = new Manager($this->config);

    $manager->instance('missing');
})->throws(InvalidArgumentException::class);

it('throws when the instance has no URL configured', function () {
    $config = $this->config;
    $config['instances']['default']['url'] = null;

    (new Manager($config))->instance();
})->throws(InvalidArgumentException::class, 'no URL configured');

it('builds a signed URL for the default instance', function () {
    $manager = new Manager($this->config);

    expect($manager->image('http://example.com/image.jpg'))->toBeInstanceOf(Builder::class)
        ->and($manager->image('http://example.com/image.jpg')->url())
        ->toBe('https://imgproxy.example.com/-21kNUD97Cxp5oC7jAkCwnb4P6SgSaavMwg6PQVZzFU/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('builds a URL for a named instance with its own settings', function () {
    $manager = new Manager($this->config);

    expect($manager->image('http://example.com/image.jpg', 'staging')->url())
        ->toBe('https://imgproxy.staging.example.com/MpZcoK1O2EQ/plain/http%3A%2F%2Fexample.com%2Fimage.jpg');
});
