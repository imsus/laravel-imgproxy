<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Manager;

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
                'key' => null,
                'salt' => null,
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

    expect($manager->instance())->toBe($this->config['instances']['default']);
});

it('resolves a named instance', function () {
    $manager = new Manager($this->config);

    expect($manager->instance('staging'))->toBe($this->config['instances']['staging']);
});

it('throws when the instance is not configured', function () {
    $manager = new Manager($this->config);

    $manager->instance('missing');
})->throws(InvalidArgumentException::class);
