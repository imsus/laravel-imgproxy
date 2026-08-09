<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Builder;

it('encodes the source with URL-safe base64 without padding', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://img.example.com/example.jpg');

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2ltZy5leGFtcGxlLmNvbS9leGFtcGxlLmpwZw');
});

it('uses the URL-safe alphabet for base64 output', function () {
    $builder = new Builder('http://imgproxy.example.com', 'https://e.com/ü');

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/aHR0cHM6Ly9lLmNvbS_DvA')
        ->and(base64_encode('https://e.com/ü'))->toContain('/');
});

it('percent-encodes the source in plain mode', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/images/curiosity.jpg', 'plain');

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/plain/http%3A%2F%2Fexample.com%2Fimages%2Fcuriosity.jpg');
});

it('assembles option segments in order between the signature slot and the source', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg', segments: [
        'rs:fill:300:400:0',
        'g:sm',
    ]);

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('normalizes a trailing slash on the base URL', function () {
    $builder = new Builder('http://imgproxy.example.com/', 'http://img.example.com/example.jpg');

    expect($builder->url())->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2ltZy5leGFtcGxlLmNvbS9leGFtcGxlLmpwZw');
});

it('returns the same URL from __toString', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://img.example.com/example.jpg');

    expect((string) $builder)->toBe($builder->url());
});

it('returns a new instance when the encoding changes', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://img.example.com/example.jpg');
    $plain = $builder->encoding('plain');

    expect($plain)->not->toBe($builder)
        ->and($builder->url())->toContain('/unsafe/aHR0cDov')
        ->and($plain->url())->toContain('/unsafe/plain/http%3A%2F%2F');
});

it('throws when the encoding is not supported', function () {
    new Builder('http://imgproxy.example.com', 'http://img.example.com/example.jpg', 'hex');
})->throws(InvalidArgumentException::class);
