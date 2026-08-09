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

it('renders AVIF and WebP sources with a JPG fallback img', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" alt="Hero" class="hero" />');

    expect($html)
        ->toContain('<picture>')
        ->toContain('<source srcset="https://imgproxy.example.com/unsafe/f:avif/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw" type="image/avif"')
        ->toContain('<source srcset="https://imgproxy.example.com/unsafe/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw" type="image/webp"')
        ->toContain('src="https://imgproxy.example.com/unsafe/f:jpg/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"')
        ->toContain('alt="Hero"')
        ->toContain('loading="lazy"')
        ->toContain('class="hero"')
        ->toContain('</picture>');
});

it('builds srcsets from widths on every source', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" :widths="[320, 640]" sizes="100vw" />');

    expect($html)
        ->toContain('srcset="https://imgproxy.example.com/unsafe/f:avif/w:320/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 320w, https://imgproxy.example.com/unsafe/f:avif/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"')
        ->toContain('srcset="https://imgproxy.example.com/unsafe/f:webp/w:320/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 320w, https://imgproxy.example.com/unsafe/f:webp/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"')
        ->toContain('sizes="100vw"');
});

it('renders only the configured formats', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" :formats="[\'webp\', \'png\']" />');

    expect($html)
        ->toContain('<source srcset="https://imgproxy.example.com/unsafe/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw" type="image/webp"')
        ->toContain('src="https://imgproxy.example.com/unsafe/f:png/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('accepts a comma-separated formats string', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" formats="avif, webp" />');

    expect($html)
        ->toContain('type="image/avif"')
        ->toContain('src="https://imgproxy.example.com/unsafe/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('renders an img-only picture for a single format', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" :formats="[\'avif\']" />');

    expect($html)
        ->not->toContain('<source')
        ->toContain('src="https://imgproxy.example.com/unsafe/f:avif/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('composes a preset on sources and the fallback img', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" preset="thumb" />');

    expect($html)
        ->toContain('rs:fill/w:300/h:300/f:avif/')
        ->toContain('rs:fill/w:300/h:300/f:jpg/');
});

it('pairs an LQIP placeholder with the fallback img srcset', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" placeholder :widths="[640]" />');

    expect($html)
        ->toContain('src="https://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"')
        ->toContain('srcset="https://imgproxy.example.com/unsafe/f:jpg/w:640/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw 640w"');
});

it('keeps the fallback image reachable for a placeholder without width candidates', function () {
    $html = Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" placeholder />');

    expect($html)
        ->toContain('srcset="https://imgproxy.example.com/unsafe/f:jpg/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw"');
});

it('builds sources from a disk and path', function () {
    $html = Blade::render('<x-imgproxy-picture disk="public" path="images/photo.jpg" />');

    expect($html)
        ->toContain('aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('throws on an unknown format', function () {
    Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" :formats="[\'bogus\']" />');
})->throws(ViewException::class, 'format [bogus] is not supported');

it('throws when widths and DPRs are combined', function () {
    Blade::render('<x-imgproxy-picture src="http://example.com/image.jpg" :widths="[320]" :dprs="[2]" />');
})->throws(ViewException::class, 'cannot combine widths and DPRs');
