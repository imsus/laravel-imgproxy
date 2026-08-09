<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Builder;
use LaravelImgproxy\LaravelImgproxy\Enums\Format;
use LaravelImgproxy\LaravelImgproxy\Enums\Gravity;
use LaravelImgproxy\LaravelImgproxy\Enums\ResizeType;
use LaravelImgproxy\LaravelImgproxy\Enums\WatermarkPosition;

/*
|--------------------------------------------------------------------------
| Golden vectors
|--------------------------------------------------------------------------
*/

it('pins the full signed URL of a typed fluent chain', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574', // "secret"
        salt: '68656C6C6F',  // "hello"
    );

    $url = $builder
        ->resize(ResizeType::Fill, 300, 400)
        ->gravity(Gravity::Smart)
        ->quality(80)
        ->format(Format::Webp)
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/PWDkpEiZ0-N_4VTotbUNBZmZVTDd_qzk3wHKwTj9QFM/rs:fill:300:400/g:sm/q:80/f:webp/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('pins the full signed URL of a chain mixing typed options and raw segments', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574',
        salt: '68656C6C6F',
    );

    $url = $builder
        ->width(300)
        ->height(400)
        ->crop(300, 400, Gravity::SouthEast)
        ->gravity(Gravity::Center, 10, 10)
        ->dpr(2)
        ->blur(1.5)
        ->sharpen(0.7)
        ->rotate(90)
        ->enlarge(true)
        ->extend(true, Gravity::SouthEast)
        ->background('#1d1d1d')
        ->watermark(0.5, WatermarkPosition::SouthEast, 10, 10, 0.2)
        ->raw('cb:v2')
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/2j1YbJCRSLKVUxXxVFvG0ZRbB7s-JMjA-fRatrA12Hs/w:300/h:400/c:300:400:soea/g:ce:10:10/dpr:2/bl:1.5/sh:0.7/rot:90/en:1/ex:1:soea/bg:1d1d1d/wm:0.5:soea:10:10:0.2/cb:v2/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

/*
|--------------------------------------------------------------------------
| Option segment composition
|--------------------------------------------------------------------------
*/

it('composes the resize option from type, size, enlarge, and extend', function () {
    $builder = fn (): Builder => new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder()->resize(ResizeType::Fit)->url())->toContain('/unsafe/rs:fit/')
        ->and($builder()->resize(ResizeType::Fill, 300, 400)->url())->toContain('/unsafe/rs:fill:300:400/')
        ->and($builder()->resize(ResizeType::Fill, 300, 400, enlarge: true)->url())->toContain('/unsafe/rs:fill:300:400:1/')
        ->and($builder()->resize(ResizeType::Fill, 300, 400, enlarge: true, extend: true)->url())->toContain('/unsafe/rs:fill:300:400:1:1/')
        ->and($builder()->resize(ResizeType::Fill, 300, 400, extend: true)->url())->toContain('/unsafe/rs:fill:300:400:0:1/');
});

it('accepts the resize type as a string', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->resize('fill', 300, 400)->url())->toContain('/unsafe/rs:fill:300:400/');
});

it('composes width, height, and quality options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->width(300)->height(400)->quality(80)->url())
        ->toBe('http://imgproxy.example.com/unsafe/w:300/h:400/q:80/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('keeps the option order of the calls', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->height(400)->width(300)->url())->toContain('/unsafe/h:400/w:300/');
});

it('composes the format option from the enum or its value', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->format(Format::Webp)->url())->toContain('/unsafe/f:webp/')
        ->and($builder->format('avif')->url())->toContain('/unsafe/f:avif/');
});

it('composes the crop option with and without gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->crop(300, 400)->url())->toContain('/unsafe/c:300:400/')
        ->and($builder->crop(0.5, 0.5, Gravity::SouthEast)->url())->toContain('/unsafe/c:0.5:0.5:soea/');
});

it('composes the gravity option with and without offsets', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->gravity(Gravity::Smart)->url())->toContain('/unsafe/g:sm/')
        ->and($builder->gravity(Gravity::Center, 10, 10)->url())->toContain('/unsafe/g:ce:10:10/')
        ->and($builder->gravity(Gravity::Center, 0, 10)->url())->toContain('/unsafe/g:ce:0:10/')
        ->and($builder->gravity('sm')->url())->toContain('/unsafe/g:sm/');
});

it('composes the dpr, blur, and sharpen options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->dpr(2)->url())->toContain('/unsafe/dpr:2/')
        ->and($builder->dpr(1.5)->url())->toContain('/unsafe/dpr:1.5/')
        ->and($builder->blur(1.5)->url())->toContain('/unsafe/bl:1.5/')
        ->and($builder->sharpen(0.7)->url())->toContain('/unsafe/sh:0.7/');
});

it('composes the rotate option', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->rotate(90)->url())->toContain('/unsafe/rot:90/')
        ->and($builder->rotate(270)->url())->toContain('/unsafe/rot:270/');
});

it('composes the enlarge and extend options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->enlarge(true)->url())->toContain('/unsafe/en:1/')
        ->and($builder->enlarge(false)->url())->toContain('/unsafe/en:0/')
        ->and($builder->extend(true)->url())->toContain('/unsafe/ex:1/')
        ->and($builder->extend(false)->url())->toContain('/unsafe/ex:0/')
        ->and($builder->extend(true, Gravity::SouthEast)->url())->toContain('/unsafe/ex:1:soea/');
});

it('composes the background option from a hex color', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->background('1d1d1d')->url())->toContain('/unsafe/bg:1d1d1d/')
        ->and($builder->background('#fff')->url())->toContain('/unsafe/bg:fff/')
        ->and($builder->background('#1D1D1D')->url())->toContain('/unsafe/bg:1D1D1D/');
});

it('composes the watermark option from all its arguments', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->watermark(0.5)->url())->toContain('/unsafe/wm:0.5/')
        ->and($builder->watermark(0.5, WatermarkPosition::SouthEast)->url())->toContain('/unsafe/wm:0.5:soea/')
        ->and($builder->watermark(0.5, WatermarkPosition::SouthEast, 10, 10)->url())->toContain('/unsafe/wm:0.5:soea:10:10/')
        ->and($builder->watermark(0.5, WatermarkPosition::SouthEast, 10, 10, 0.2)->url())->toContain('/unsafe/wm:0.5:soea:10:10:0.2/')
        ->and($builder->watermark(0.5, WatermarkPosition::Repeat)->url())->toContain('/unsafe/wm:0.5:re/');
});

it('emits the default center position when watermark offsets are set without a position', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->watermark(0.5, xOffset: 10)->url())->toContain('/unsafe/wm:0.5:ce:10/');
});

it('appends raw segments verbatim in call order', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->width(300)->raw('cb:abc')->quality(80)->url())
        ->toBe('http://imgproxy.example.com/unsafe/w:300/cb:abc/q:80/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('appends raw segments without validation', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->raw('anything:goes:here')->url())->toContain('/unsafe/anything:goes:here/');
});

/*
|--------------------------------------------------------------------------
| Immutability
|--------------------------------------------------------------------------
*/

it('keeps the base builder unchanged when options are applied', function () {
    $base = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');
    $variant = $base->width(300)->height(400);

    expect($variant)->not->toBe($base)
        ->and($base->url())->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc')
        ->and($variant->url())->toBe('http://imgproxy.example.com/unsafe/w:300/h:400/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('reuses a base builder for several URL variants', function () {
    $base = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $square = $base->resize(ResizeType::Fill, 300, 300);
    $wide = $base->resize(ResizeType::Fill, 600, 200);

    expect($square->url())->toContain('/unsafe/rs:fill:300:300/')
        ->and($wide->url())->toContain('/unsafe/rs:fill:600:200/');
});

/*
|--------------------------------------------------------------------------
| Enum contracts
|--------------------------------------------------------------------------
*/

it('defines the imgproxy resize types', function () {
    expect(ResizeType::cases())
        ->toBe([
            ResizeType::Fit,
            ResizeType::Fill,
            ResizeType::FillDown,
            ResizeType::Force,
            ResizeType::Auto,
        ])
        ->and(array_column(ResizeType::cases(), 'value'))
        ->toBe(['fit', 'fill', 'fill-down', 'force', 'auto']);
});

it('defines the imgproxy gravity types', function () {
    expect(array_column(Gravity::cases(), 'value'))
        ->toBe(['ce', 'no', 'so', 'ea', 'we', 'nowe', 'noea', 'sowe', 'soea', 'sm']);
});

it('defines the imgproxy output formats', function () {
    expect(array_column(Format::cases(), 'value'))
        ->toBe(['jpg', 'png', 'webp', 'avif', 'gif', 'ico', 'svg', 'bmp', 'tiff', 'heic', 'jxl']);
});

it('defines the imgproxy watermark positions', function () {
    expect(array_column(WatermarkPosition::cases(), 'value'))
        ->toBe(['ce', 'no', 'so', 'ea', 'we', 'nowe', 'noea', 'sowe', 'soea', 're']);
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

it('throws when the resize type is unknown', function () {
    new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg')->resize('bogus', 300, 400);
})->throws(InvalidArgumentException::class);

it('throws when a resize size is negative', function () {
    new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg')->resize(ResizeType::Fill, -1, 400);
})->throws(InvalidArgumentException::class);

it('throws when the width or height is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->width(-1);
})->throws(InvalidArgumentException::class);

it('throws when the quality is outside 0-100', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->quality(101);
})->throws(InvalidArgumentException::class);

it('throws when the format is unknown', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->format('bogus');
})->throws(InvalidArgumentException::class);

it('throws when a crop size is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->crop(-1, 400);
})->throws(InvalidArgumentException::class);

it('throws when the gravity is unknown', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->gravity('bogus');
})->throws(InvalidArgumentException::class);

it('throws when the dpr is not greater than zero', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->dpr(0);
})->throws(InvalidArgumentException::class);

it('throws when the blur or sharpen sigma is not greater than zero', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->blur(0);
})->throws(InvalidArgumentException::class);

it('throws when the rotate angle is not a non-negative multiple of 90', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->rotate(45);
})->throws(InvalidArgumentException::class);

it('throws when the extend gravity is the smart gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->extend(true, Gravity::Smart);
})->throws(InvalidArgumentException::class);

it('throws when the background color is not a hex value', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->background('not-a-color');
})->throws(InvalidArgumentException::class);

it('throws when the watermark opacity is outside 0-1', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->watermark(0);
})->throws(InvalidArgumentException::class);

it('throws when the watermark position is unknown', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->watermark(0.5, 'bogus');
})->throws(InvalidArgumentException::class);

it('throws when the watermark scale is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->watermark(0.5, scale: -1);
})->throws(InvalidArgumentException::class);
