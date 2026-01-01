<?php

use Imsus\ImgProxy\Components\Img;
use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

beforeEach(function () {
    $this->component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
    );
});

it('builds url with default values', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
    );

    $url = $component->buildUrl();

    expect($url)->toStartWith('http://localhost:8080/');
});

it('builds url with width and height', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
    );

    $url = $component->buildUrl();

    expect($url)->toContain('width:300')
        ->and($url)->toContain('height:200');
});

it('builds url with resize type', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
        resizeType: ResizeType::FILL,
    );

    expect($component->buildUrl())->toContain('resizing_type:fill');
});

it('builds url with output format', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        format: OutputExtension::WEBP,
    );

    expect($component->buildUrl())->toEndWith('.webp');
});

it('builds url with quality', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        quality: 80,
    );

    expect($component->buildUrl())->toContain('quality:80');
});

it('builds url with dpr', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        dpr: 2,
    );

    expect($component->buildUrl())->toContain('dpr:2');
});

it('builds url with gravity', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
        resizeType: ResizeType::FILL,
        gravity: Gravity::CENTER,
    );

    expect($component->buildUrl())->toContain('gravity:ce');
});

it('builds url with all options combined', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 800,
        height: 600,
        resizeType: ResizeType::FILL,
        format: OutputExtension::WEBP,
        quality: 85,
        dpr: 2,
        gravity: Gravity::CENTER,
    );

    $url = $component->buildUrl();

    expect($url)->toContain('width:800')
        ->and($url)->toContain('height:600')
        ->and($url)->toContain('resizing_type:fill')
        ->and($url)->toEndWith('.webp')
        ->and($url)->toContain('quality:85')
        ->and($url)->toContain('dpr:2')
        ->and($url)->toContain('gravity:ce');
});

it('does not include quality when set to 0', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
        quality: 0,
    );

    $url = $component->buildUrl();

    expect($url)->toContain('width:300')
        ->and($url)->toContain('height:200')
        ->and($url)->not->toContain('quality:');
});

it('does not include quality when default is used', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
    );

    $url = $component->buildUrl();

    // Default quality is 75, so it should be included
    expect($url)->toContain('quality:75');
});

it('works with lazy loading disabled', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
        lazy: false,
    );

    $url = $component->buildUrl();

    expect($url)->toContain('width:300')
        ->and($url)->toContain('height:200');
});

it('works with sizes attribute', function () {
    $component = new Img(
        src: 'https://example.com/image.jpg',
        alt: 'Test image',
        width: 300,
        height: 200,
        sizes: '(max-width: 768px) 100vw, 50vw',
    );

    $url = $component->buildUrl();

    expect($url)->toContain('width:300')
        ->and($url)->toContain('height:200');
});
