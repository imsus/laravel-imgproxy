<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\Gravity;
use Imsus\LaravelImgproxy\Enums\ResizeType;

/*
|--------------------------------------------------------------------------
| Golden vectors
|--------------------------------------------------------------------------
*/

it('pins the full signed URL of an intent chain', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574', // "secret"
        salt: '68656C6C6F',  // "hello"
    );

    $url = $builder
        ->cover(300, 300)
        ->quality(80)
        ->toWebp()
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/LDgmVQbhaeyygRF-VqbU8dTJSssUqwpwQ0u8ShWmAm4/rs:fill:300:300/q:80/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('pins the full signed URL of a cover call gravity-anchored', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574',
        salt: '68656C6C6F',
    );

    expect($builder->cover(300, 300, Gravity::North)->url())
        ->toBe('http://imgproxy.example.com/Koes3fa6y0nMtR1H16jZsreTeuRlIdkkOAzVCeMhTQA/rs:fill:300:300/g:no/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('pins the full signed URL of a when-conditioned chain', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574',
        salt: '68656C6C6F',
    );

    $url = $builder
        ->when(true, fn (Builder $b) => $b->cover(300, 300)->quality(80))
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/i4cKXArxg-wQy3o9At2prVI7ryBWVvcEdwtBxx-bxY4/rs:fill:300:300/q:80/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

/*
|--------------------------------------------------------------------------
| Intent method composition
|--------------------------------------------------------------------------
*/

it('composes the cover option from size and optional gravity', function () {
    $builder = fn (): Builder => new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder()->cover(300, 300)->url())->toContain('/unsafe/rs:fill:300:300/')
        ->and($builder()->cover(300, 300, Gravity::SouthEast)->url())->toContain('/unsafe/rs:fill:300:300/g:soea/')
        ->and($builder()->cover(300, 300, 'nowe')->url())->toContain('/unsafe/rs:fill:300:300/g:nowe/');
});

it('composes the fit option from size', function () {
    $builder = fn (): Builder => new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder()->fit(400, 300)->url())->toContain('/unsafe/rs:fit:400:300/');
});

it('composes the orient and flip intent options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->orient()->url())->toContain('/unsafe/ar:1/')
        ->and($builder->flipVertically()->url())->toContain('/unsafe/fl:0:1/')
        ->and($builder->flipHorizontally()->url())->toContain('/unsafe/fl:1:0/');
});

it('composes the format shortcut intent options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->toWebp()->url())->toContain('/unsafe/f:webp/')
        ->and($builder->toJpg()->url())->toContain('/unsafe/f:jpg/')
        ->and($builder->toPng()->url())->toContain('/unsafe/f:png/')
        ->and($builder->toAvif()->url())->toContain('/unsafe/f:avif/');
});

it('composes the optimize option with default and explicit values', function () {
    $builder = fn (): Builder => new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder()->optimize()->url())->toContain('/unsafe/f:webp/q:70/')
        ->and($builder()->optimize()->url())->toContain('/unsafe/f:webp/q:70/')
        ->and($builder()->optimize(Format::Avif, 85)->url())->toContain('/unsafe/f:avif/q:85/')
        ->and($builder()->optimize('jpg')->url())->toContain('/unsafe/f:jpg/q:70/');
});

it('composes cover with downstream options in call order', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->cover(400, 400, Gravity::Center)->quality(80)->toWebp()->url())
        ->toContain('/unsafe/rs:fill:400:400/g:ce/q:80/f:webp/');
});

it('keeps the base builder unchanged when intent methods are applied', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $covered = $builder->cover(300, 300);

    expect($covered)->not->toBe($builder)
        ->and($builder->url())->not->toContain('rs:fill')
        ->and($covered->url())->toContain('/unsafe/rs:fill:300:300/');
});

it('builds a placeholder and a full-size image from one base builder with when', function () {
    $base = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg')->cover(400, 400);

    $placeholder = $base->when(true, fn (Builder $b) => $b->width(16)->blur(8)->toWebp());
    $full = $base->when(false, fn (Builder $b) => $b->width(16)->blur(8)->toWebp());

    expect($placeholder->url())->toContain('/unsafe/rs:fill:400:400/w:16/bl:8/f:webp/')
        ->and($full->url())->toContain('/unsafe/rs:fill:400:400/');
});

/*
|--------------------------------------------------------------------------
| Conditionable
|--------------------------------------------------------------------------
*/

it('applies the callback when the value resolves truthy', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->when(true, fn (Builder $b) => $b->quality(80))->url())->toContain('/unsafe/q:80/')
        ->and($builder->when(false, fn (Builder $b) => $b->quality(80))->url())->not->toContain('/unsafe/q:80/');
});

it('applies the callback when the value resolves falsy through unless', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->unless(false, fn (Builder $b) => $b->quality(80))->url())->toContain('/unsafe/q:80/')
        ->and($builder->unless(true, fn (Builder $b) => $b->quality(80))->url())->not->toContain('/unsafe/q:80/');
});

it('supports a closure condition value', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->when(fn () => 1 > 0, fn (Builder $b) => $b->quality(80))->url())->toContain('/unsafe/q:80/');
});

/*
|--------------------------------------------------------------------------
| resize regression guard
|--------------------------------------------------------------------------
*/

it('keeps resize emitting the rs segment (deprecation is docblock-only)', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->resize(ResizeType::Fill, 300, 400)->url())->toContain('/unsafe/rs:fill:300:400/')
        ->and($builder->resize(ResizeType::Fit, 300, 300)->url())->toContain('/unsafe/rs:fit:300:300/');
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

it('throws when the cover size is negative', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg'))->cover(-1, 300);
})->throws(InvalidArgumentException::class);

it('throws when the fit size is negative', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg'))->fit(300, -1);
})->throws(InvalidArgumentException::class);

it('throws when the cover gravity is unknown', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg'))->cover(300, 300, 'bogus');
})->throws(InvalidArgumentException::class);

it('throws when the optimize format is unknown', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg'))->optimize('bogus');
})->throws(InvalidArgumentException::class);

it('throws when the optimize quality is outside 0-100', function () {
    (new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg'))->optimize(quality: 101);
})->throws(InvalidArgumentException::class);
