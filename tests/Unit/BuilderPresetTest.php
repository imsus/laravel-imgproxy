<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\ResizeType;

it('composes a configured preset onto the builder', function () {
    $builder = new Builder(
        'https://imgproxy.example.com',
        'http://example.com/image.jpg',
        'base64',
        [],
        'a1b2c3d4',
        'e5f60718',
        null,
        ['thumb' => ['resize' => 'fill', 'width' => 300, 'height' => 300]],
    );

    expect($builder->applyPreset('thumb')->url())
        ->toBe('https://imgproxy.example.com/Q4o_jv7N4mdlFZOZ2RCk-c-5bmm40pVxE40qYzOwfiU/rs:fill/w:300/h:300/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('appends per-URL overrides after preset options so overrides win', function () {
    $builder = new Builder(
        'https://imgproxy.example.com',
        'http://example.com/image.jpg',
        'base64',
        [],
        'a1b2c3d4',
        'e5f60718',
        null,
        ['thumb' => ['resize' => 'fill', 'width' => 300, 'height' => 300]],
    );

    expect($builder->applyPreset('thumb')->width(400)->url())
        ->toBe('https://imgproxy.example.com/1U1YMf9yu3owDLfBcdMFsrs776PK4tJjgdsISPLsgCs/rs:fill/w:300/h:300/w:400/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('accepts enum instances as preset values', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image.jpg',
        presets: [
            'card' => ['resize' => ResizeType::Fill, 'width' => 600, 'format' => Format::Webp],
        ],
    );

    expect($builder->applyPreset('card')->url())
        ->toBe('http://imgproxy.example.com/unsafe/rs:fill/w:600/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('keeps presets when the source encoding changes', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image.jpg',
        presets: ['thumb' => ['width' => 300]],
    );

    expect($builder->sourceEncoding('plain')->applyPreset('thumb')->url())
        ->toBe('http://imgproxy.example.com/unsafe/w:300/plain/http%3A%2F%2Fexample.com%2Fimage.jpg');
});

it('returns a new instance when a preset is applied', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg', presets: [
        'thumb' => ['width' => 300],
    ]);

    $thumb = $builder->applyPreset('thumb');

    expect($thumb)->not->toBe($builder)
        ->and($builder->url())->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('throws when the preset is not configured', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg'))->applyPreset('missing');
})->throws(InvalidArgumentException::class, 'preset [missing] is not configured');

it('throws when the preset option is not supported', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg', presets: [
        'thumb' => ['nope' => 1],
    ]))->applyPreset('thumb');
})->throws(InvalidArgumentException::class, 'preset option [nope] is not supported');

it('throws when the preset value has the wrong type', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg', presets: [
        'thumb' => ['width' => '300'],
    ]))->applyPreset('thumb');
})->throws(InvalidArgumentException::class, 'preset option [width] must be an integer');

it('validates preset values through the typed methods', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg', presets: [
        'thumb' => ['quality' => 150],
    ]))->applyPreset('thumb');
})->throws(InvalidArgumentException::class);

it('builds a signed placeholder URL for the same source', function () {
    $builder = new Builder(
        'https://imgproxy.example.com',
        'http://example.com/image.jpg',
        'base64',
        [],
        'a1b2c3d4',
        'e5f60718',
    );

    expect($builder->placeholder()->url())
        ->toBe('https://imgproxy.example.com/aiTFQ5e58WXp6lHtQJJkg3O-Oz4dW42kixk0nRg98m4/w:16/bl:8/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('builds an unsigned placeholder URL when no key is configured', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg');

    expect($builder->placeholder()->url())
        ->toBe('http://imgproxy.example.com/unsafe/w:16/bl:8/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw');
});

it('keeps the original builder unchanged when a placeholder is built', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image.jpg');

    $placeholder = $builder->placeholder();

    expect($builder->url())->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlLmpwZw')
        ->and($placeholder->url())->toContain('/w:16/bl:8/f:webp/');
});
