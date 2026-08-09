<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Instance;
use LaravelImgproxy\LaravelImgproxy\Manager;

it('registers the manager as a singleton', function () {
    expect(app(Manager::class))->toBeInstanceOf(Manager::class);
});

it('returns the same manager instance from the container', function () {
    expect(app(Manager::class))->toBe(app(Manager::class));
});

it('merges the package config', function () {
    expect(config('laravel-imgproxy.default'))->toBe('default');
});

it('defines the default instance with the full option surface', function () {
    $instance = config('laravel-imgproxy.instances.default');

    expect($instance)->toHaveKeys(['url', 'key', 'salt', 'signature_size', 'encoding'])
        ->and($instance['signature_size'])->toBeNull()
        ->and($instance['encoding'])->toBe('base64');
});

it('defines the presets section', function () {
    expect(config('laravel-imgproxy.presets'))->toBeArray()->toBeEmpty();
});

it('resolves the default instance through the manager', function () {
    config()->set('laravel-imgproxy.instances.default', [
        'url' => 'https://imgproxy.example.com',
        'key' => null,
        'salt' => null,
        'signature_size' => null,
        'encoding' => 'base64',
    ]);

    $manager = app(Manager::class);

    expect($manager->defaultInstance())->toBe('default')
        ->and($manager->instance())->toBeInstanceOf(Instance::class)
        ->and($manager->instance()->baseUrl())->toBe('https://imgproxy.example.com');
});

it('reads the default instance connection from the environment', function () {
    putenv('IMGPROXY_URL=https://imgproxy.example.com');
    putenv('IMGPROXY_KEY=a1b2c3d4');
    putenv('IMGPROXY_SALT=e5f60718');

    try {
        $config = require __DIR__.'/../../config/laravel-imgproxy.php';

        expect($config['instances']['default'])->toBe([
            'url' => 'https://imgproxy.example.com',
            'key' => 'a1b2c3d4',
            'salt' => 'e5f60718',
            'signature_size' => null,
            'encoding' => 'base64',
        ]);
    } finally {
        putenv('IMGPROXY_URL');
        putenv('IMGPROXY_KEY');
        putenv('IMGPROXY_SALT');
    }
});
