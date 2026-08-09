<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\View\ViewException;

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
        'presets' => [
            'thumb' => ['resize' => 'fill', 'width' => 300, 'height' => 300],
        ],
    ]);

    config()->set('filesystems.disks.public.url', 'https://cdn.example.com');
});

it('renders an img with src, alt, lazy loading, and passed classes', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" alt="A photo" class="rounded shadow" />');

    expect($html)
        ->toContain('src="https://imgproxy.example.com/unsafe/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"')
        ->toContain('alt="A photo"')
        ->toContain('loading="lazy"')
        ->toContain('class="rounded shadow"');
});

it('builds a srcset from width candidates', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" :widths="[320, 640]" sizes="100vw" />');

    expect($html)
        ->toContain('srcset="https://imgproxy.example.com/unsafe/w:320/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 320w, https://imgproxy.example.com/unsafe/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"')
        ->toContain('sizes="100vw"');
});

it('builds a srcset from DPR candidates', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" :dprs="[1, 2]" />');

    expect($html)
        ->toContain('srcset="https://imgproxy.example.com/unsafe/dpr:1/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 1x, https://imgproxy.example.com/unsafe/dpr:2/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 2x"');
});

it('accepts a comma-separated widths string', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" widths="320, 640" />');

    expect($html)
        ->toContain('w:320/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 320w, https://imgproxy.example.com/unsafe/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w');
});

it('accepts a comma-separated DPR string', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" dprs="1.5, 2" />');

    expect($html)
        ->toContain('dpr:1.5/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 1.5x, https://imgproxy.example.com/unsafe/dpr:2/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 2x');
});

it('composes a preset before per-URL width overrides', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" preset="thumb" :widths="[640]" />');

    expect($html)
        ->toContain('srcset="https://imgproxy.example.com/unsafe/rs:fill/w:300/h:300/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"')
        ->toContain('src="https://imgproxy.example.com/unsafe/rs:fill/w:300/h:300/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('pairs an LQIP placeholder with the full srcset', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" placeholder :widths="[640]" />');

    expect($html)
        ->toContain('src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"')
        ->toContain('srcset="https://imgproxy.example.com/unsafe/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"');
});

it('keeps the full image reachable for a placeholder without width candidates', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" placeholder />');

    expect($html)
        ->toContain('src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"')
        ->toContain('srcset="https://imgproxy.example.com/unsafe/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('builds the source from a disk and path', function () {
    $html = Blade::render('<x-imgproxy-img disk="public" path="images/photo.jpg" alt="Photo" />');

    expect($html)
        ->toContain('src="https://imgproxy.example.com/unsafe/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw"')
        ->toContain('alt="Photo"');
});

it('supports eager loading and explicit sizes', function () {
    $html = Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" loading="eager" sizes="(min-width: 768px) 50vw, 100vw" />');

    expect($html)
        ->toContain('loading="eager"')
        ->toContain('sizes="(min-width: 768px) 50vw, 100vw"');
});

it('throws when no source is given', function () {
    Blade::render('<x-imgproxy-img />');
})->throws(ViewException::class, 'requires a source URL or a disk and path');

it('throws when only one of disk and path is given', function () {
    Blade::render('<x-imgproxy-img disk="public" />');
})->throws(ViewException::class, 'requires both a disk and a path');

it('throws when both a source URL and a disk are given', function () {
    Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" disk="public" path="images/photo.jpg" />');
})->throws(ViewException::class, 'not both');

it('throws when widths and DPRs are combined', function () {
    Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" :widths="[320]" :dprs="[2]" />');
})->throws(ViewException::class, 'cannot combine widths and DPRs');

it('throws on a non-positive width', function () {
    Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" :widths="[0]" />');
})->throws(ViewException::class, 'positive integer');

it('throws on an unknown preset', function () {
    Blade::render('<x-imgproxy-img src="http://example.com/image.jpg" preset="missing" />');
})->throws(ViewException::class, 'preset [missing] is not configured');
