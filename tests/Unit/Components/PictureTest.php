<?php

use Imsus\ImgProxy\Components\Picture;
use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

beforeEach(function () {
    $this->component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
    );
});

it('builds urls for each format', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        formats: [OutputExtension::WEBP, OutputExtension::AVIF, OutputExtension::JPEG],
    );

    $urls = $component->buildUrls();

    expect($urls)->toHaveCount(3);
    expect(array_keys($urls))->toBe(['webp', 'avif', 'jpg']);
});

it('builds urls with correct format extensions', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        formats: [OutputExtension::WEBP, OutputExtension::JPEG],
    );

    $urls = $component->buildUrls();

    expect($urls['webp'])->toEndWith('.webp')
        ->and($urls['jpg'])->toEndWith('.jpg');
});

it('builds urls with width and height', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('width:800')->and($url)->toContain('height:600');
    }
});

it('builds urls with quality', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        quality: 80,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('quality:80');
    }
});

it('builds urls with dpr', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        dpr: 2,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('dpr:2');
    }
});

it('fallback url returns last format', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        formats: [OutputExtension::WEBP, OutputExtension::AVIF, OutputExtension::JPEG],
    );

    $fallback = $component->fallbackUrl();

    expect($fallback)->toEndWith('.jpg');
});

it('builds urls with resize type and gravity', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
        resizeType: ResizeType::FILL,
        gravity: Gravity::CENTER,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('resizing_type:fill')
            ->and($url)->toContain('gravity:ce');
    }
});

it('uses default formats when not specified', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
    );

    $urls = $component->buildUrls();

    expect($urls)->toHaveCount(3);
    expect(array_keys($urls))->toBe(['webp', 'avif', 'jpg']);
});

it('works with single format', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        formats: [OutputExtension::WEBP],
    );

    $urls = $component->buildUrls();

    expect($urls)->toHaveCount(1);
    expect(array_keys($urls))->toBe(['webp']);
});

it('fallback url with single format', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        formats: [OutputExtension::AVIF],
    );

    $fallback = $component->fallbackUrl();

    expect($fallback)->toEndWith('.avif');
});

it('works with lazy loading disabled', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
        lazy: false,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('width:800')->and($url)->toContain('height:600');
    }
});

it('works with sizes attribute', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
        sizes: '(max-width: 768px) 100vw, 50vw',
    );

    $urls = $component->buildUrls();

    foreach ($urls as $url) {
        expect($url)->toContain('width:800')->and($url)->toContain('height:600');
    }
});

it('builds urls with all options combined', function () {
    $component = new Picture(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
        resizeType: ResizeType::FILL,
        formats: [OutputExtension::WEBP, OutputExtension::AVIF],
        quality: 85,
        dpr: 2,
        gravity: Gravity::CENTER,
    );

    $urls = $component->buildUrls();

    foreach ($urls as $format => $url) {
        expect($url)->toContain('width:800')
            ->and($url)->toContain('height:600')
            ->and($url)->toContain('resizing_type:fill')
            ->and($url)->toContain('quality:85')
            ->and($url)->toContain('dpr:2')
            ->and($url)->toContain('gravity:ce');
    }
});
