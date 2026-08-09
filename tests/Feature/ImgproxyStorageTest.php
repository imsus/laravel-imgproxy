<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Imsus\LaravelImgproxy\Builder;

afterEach(function () {
    Carbon::setTestNow();
});

beforeEach(function () {
    config()->set('laravel-imgproxy', [
        'default' => 'default',
        'instances' => [
            'default' => [
                'url' => 'https://imgproxy.example.com',
                'key' => null,
                'salt' => null,
                'signature_size' => null,
                'encoding' => 'base64',
            ],
        ],
        'presets' => [],
    ]);

    config()->set('filesystems.disks.public.url', 'https://cdn.example.com');
});

it('builds a URL from a public disk url through the macro', function () {
    expect(Storage::disk('public')->imgproxy('images/photo.jpg'))->toBeInstanceOf(Builder::class)
        ->and(Storage::disk('public')->imgproxy('images/photo.jpg')->url())
        ->toBe('https://imgproxy.example.com/unsafe/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('builds a pre-signed URL from a private disk through the macro', function () {
    Carbon::setTestNow('2026-01-01 00:00:00+00:00');

    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, $expiration, array $options = []): string => 'https://signed.example.com/'.$path.'?expires='.$expiration->getTimestamp(),
    );

    expect(Storage::disk('local')->imgproxy('images/photo.jpg', 3600)->url())
        ->toBe('https://imgproxy.example.com/unsafe/aHR0cHM6Ly9zaWduZWQuZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZz9leHBpcmVzPTE3NjcyMjkyMDA');
});

it('builds a URL from a public disk through the builder disk method', function () {
    expect(imgproxy()->image('http://unused.example.com/image.jpg')->disk('public', 'images/photo.jpg')->url())
        ->toBe(Storage::disk('public')->imgproxy('images/photo.jpg')->url());
});

it('builds a pre-signed URL from a private disk through the builder disk method', function () {
    Carbon::setTestNow('2026-01-01 00:00:00+00:00');

    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, $expiration, array $options = []): string => 'https://signed.example.com/'.$path.'?expires='.$expiration->getTimestamp(),
    );

    expect(imgproxy()->image('http://unused.example.com/image.jpg')->disk('local', 'images/photo.jpg', 3600)->url())
        ->toBe('https://imgproxy.example.com/unsafe/aHR0cHM6Ly9zaWduZWQuZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZz9leHBpcmVzPTE3NjcyMjkyMDA');
});

it('keeps processing options when the source comes from a disk', function () {
    expect(imgproxy()->image('http://unused.example.com/image.jpg')->disk('public', 'images/photo.jpg')->width(300)->url())
        ->toBe('https://imgproxy.example.com/unsafe/w:300/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('prefers the plain url when a public disk can also produce temporary urls', function () {
    Storage::disk('public')->buildTemporaryUrlsUsing(
        fn (string $path): string => 'https://signed.example.com/'.$path,
    );

    expect(Storage::disk('public')->imgproxy('images/photo.jpg')->url())
        ->toBe('https://imgproxy.example.com/unsafe/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('throws when the disk is not configured', function () {
    Storage::disk('missing')->imgproxy('images/photo.jpg');
})->throws(InvalidArgumentException::class, 'Disk [missing] does not have a configured driver');
