<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Enums\Format;
use Imsus\LaravelImgproxy\Enums\Gravity;
use Imsus\LaravelImgproxy\Enums\ResizeType;
use Imsus\LaravelImgproxy\Enums\WatermarkPosition;

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

it('pins the full signed URL of a chain mixing typed options and verbatim segments', function () {
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
        ->enlarge()
        ->extend(Gravity::SouthEast)
        ->background('#1d1d1d')
        ->watermark(0.5, WatermarkPosition::SouthEast, 10, 10, 0.2)
        ->withOption('cb:v2')
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/cD9MEBmnHXa6Cnfh9rVTyjiLPlLsr88tYmTwKUKFU1g/w:300/h:400/c:300:400:soea/g:ce:10:10/dpr:2/bl:1.5/sh:0.7/rot:90/el:1/ex:1:soea/bg:1d1d1d/wm:0.5:soea:10:10:0.2/cb:v2/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('pins the full signed URL of a chain of the remaining free options', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        key: '736563726574',
        salt: '68656C6C6F',
    );

    $url = $builder
        ->resizeWithGravity(300, 400, enlarge: true, extend: true, gravity: Gravity::SouthEast)
        ->withOption('rt:fit')
        ->minWidth(100)
        ->minHeight(200)
        ->zoom(2, 1.5)
        ->extendAspectRatio(Gravity::Center)
        ->focusPoint(0.5, 0.25)
        ->trim(20, '1d1d1d', true, true)
        ->padding(10, 20, 30, 40)
        ->autoRotate()
        ->flip(true, false)
        ->pixelate(5)
        ->stripMetadata()
        ->keepCopyright()
        ->stripColorProfile()
        ->preserveHDR()
        ->enforceThumbnail()
        ->formatQuality(['webp' => 75, 'jpg' => 80])
        ->skipProcessing(Format::Png, 'webp')
        ->cacheBuster('v2')
        ->expires(4102444800)
        ->filename('curiosity.jpg')
        ->returnAttachment()
        ->preset('sharp')
        ->maxSourceResolution(25.5)
        ->maxSourceFileSize(10485760)
        ->maxAnimationFrames(10)
        ->maxAnimationFrameResolution(8.5)
        ->maxResultDimension(20000)
        ->withOption('fancy:1')
        ->url();

    expect($url)
        ->toBe('http://imgproxy.example.com/bXCXo7-zmLy4rauUAr62Vctdw70IzdboDM9AE7CdYq8/s:300:400:1:1:soea/rt:fit/mw:100/mh:200/z:2:1.5/exar:1:ce/g:fp:0.5:0.25/t:20:1d1d1d:1:1/pd:10:20:30:40/ar:1/fl:1:0/pix:5/sm:1/kcr:1/scp:1/ph:1/eth:1/fq:webp:75:jpg:80/skp:png:webp/cb:v2/exp:4102444800/fn:curiosity.jpg/att:1/pr:sharp/msr:25.5/msfs:10485760/maf:10/mafr:8.5/mrd:20000/fancy:1/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
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

    expect($builder->enlarge()->url())->toContain('/unsafe/el:1/')
        ->and($builder->withoutEnlarge()->url())->toContain('/unsafe/el:0/')
        ->and($builder->extend()->url())->toContain('/unsafe/ex:1/')
        ->and($builder->withoutExtend()->url())->toContain('/unsafe/ex:0/')
        ->and($builder->extend(Gravity::SouthEast)->url())->toContain('/unsafe/ex:1:soea/');
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

it('composes the resize-with-gravity option from width, height, enlarge, extend, and gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->resizeWithGravity(300, 400)->url())->toContain('/unsafe/s:300:400/')
        ->and($builder->resizeWithGravity(300, 400, enlarge: true)->url())->toContain('/unsafe/s:300:400:1/')
        ->and($builder->resizeWithGravity(300, 400, enlarge: true, extend: true)->url())->toContain('/unsafe/s:300:400:1:1/')
        ->and($builder->resizeWithGravity(300, 400, extend: true)->url())->toContain('/unsafe/s:300:400:0:1/')
        ->and($builder->resizeWithGravity(300, 400, enlarge: true, extend: true, gravity: Gravity::SouthEast)->url())->toContain('/unsafe/s:300:400:1:1:soea/');
});

it('composes the min width and min height options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->minWidth(100)->url())->toContain('/unsafe/mw:100/')
        ->and($builder->minHeight(200)->url())->toContain('/unsafe/mh:200/')
        ->and($builder->withOption('rt:fit')->url())->toContain('/unsafe/rt:fit/');
});

it('composes the zoom option with one or two factors', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->zoom(2)->url())->toContain('/unsafe/z:2/')
        ->and($builder->zoom(2, 1.5)->url())->toContain('/unsafe/z:2:1.5/');
});

it('composes the extend aspect ratio option with and without gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->extendAspectRatio()->url())->toContain('/unsafe/exar:1/')
        ->and($builder->withoutExtendAspectRatio()->url())->toContain('/unsafe/exar:0/')
        ->and($builder->extendAspectRatio(Gravity::Center)->url())->toContain('/unsafe/exar:1:ce/');
});

it('composes the focus point gravity option', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->focusPoint(0.5, 0.25)->url())->toContain('/unsafe/g:fp:0.5:0.25/');
});

it('composes the trim option from threshold, color, and equal flags', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->trim(20)->url())->toContain('/unsafe/t:20/')
        ->and($builder->trim(20, '1d1d1d')->url())->toContain('/unsafe/t:20:1d1d1d/')
        ->and($builder->trim(20, '1d1d1d', true)->url())->toContain('/unsafe/t:20:1d1d1d:1/')
        ->and($builder->trim(20, '1d1d1d', true, true)->url())->toContain('/unsafe/t:20:1d1d1d:1:1/')
        ->and($builder->trim(20, equalHorizontal: true)->url())->toContain('/unsafe/t:20::1/')
        ->and($builder->trim(20, equalVertical: true)->url())->toContain('/unsafe/t:20::0:1/');
});

it('composes the padding option from its sides', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->padding(10)->url())->toContain('/unsafe/pd:10/')
        ->and($builder->padding(10, 20)->url())->toContain('/unsafe/pd:10:20/')
        ->and($builder->padding(10, 20, 30)->url())->toContain('/unsafe/pd:10:20:30/')
        ->and($builder->padding(10, 20, 30, 40)->url())->toContain('/unsafe/pd:10:20:30:40/');
});

it('composes the auto rotate, flip, and pixelate options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->autoRotate()->url())->toContain('/unsafe/ar:1/')
        ->and($builder->withoutAutoRotate()->url())->toContain('/unsafe/ar:0/')
        ->and($builder->flip(true, false)->url())->toContain('/unsafe/fl:1:0/')
        ->and($builder->flip(false, true)->url())->toContain('/unsafe/fl:0:1/')
        ->and($builder->flip(true, true)->url())->toContain('/unsafe/fl:1:1/')
        ->and($builder->pixelate(5)->url())->toContain('/unsafe/pix:5/');
});

it('composes the metadata and color profile options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->stripMetadata()->url())->toContain('/unsafe/sm:1/')
        ->and($builder->keepCopyright()->url())->toContain('/unsafe/kcr:1/')
        ->and($builder->stripColorProfile()->url())->toContain('/unsafe/scp:1/')
        ->and($builder->preserveHDR()->url())->toContain('/unsafe/ph:1/')
        ->and($builder->enforceThumbnail()->url())->toContain('/unsafe/eth:1/');
});

it('composes the format quality option from format/quality pairs', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->formatQuality(['webp' => 75, 'jpg' => 80])->url())->toContain('/unsafe/fq:webp:75:jpg:80/');
});

it('composes the skip processing option from formats', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->skipProcessing(Format::Png, 'webp')->url())->toContain('/unsafe/skp:png:webp/');
});

it('composes the raw response option', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->raw()->url())->toContain('/unsafe/raw:1/')
        ->and($builder->withoutRaw()->url())->toContain('/unsafe/raw:0/');
});

it('composes the cache buster, expires, filename, and return attachment options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->cacheBuster('v2')->url())->toContain('/unsafe/cb:v2/')
        ->and($builder->expires(4102444800)->url())->toContain('/unsafe/exp:4102444800/')
        ->and($builder->expires(0)->url())->toContain('/unsafe/exp:0/')
        ->and($builder->filename('curiosity.jpg')->url())->toContain('/unsafe/fn:curiosity.jpg/')
        ->and($builder->filename('Y3VyaW9zaXR5LmpwZw', encoded: true)->url())->toContain('/unsafe/fn:Y3VyaW9zaXR5LmpwZw:1/')
        ->and($builder->returnAttachment()->url())->toContain('/unsafe/att:1/');
});

it('composes the server preset option from preset names', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->preset('sharp')->url())->toContain('/unsafe/pr:sharp/')
        ->and($builder->preset('sharp', 'thumb')->url())->toContain('/unsafe/pr:sharp:thumb/');
});

it('composes the security limit options', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->maxSourceResolution(25.5)->url())->toContain('/unsafe/msr:25.5/')
        ->and($builder->maxSourceFileSize(10485760)->url())->toContain('/unsafe/msfs:10485760/')
        ->and($builder->maxAnimationFrames(10)->url())->toContain('/unsafe/maf:10/')
        ->and($builder->maxAnimationFrameResolution(8.5)->url())->toContain('/unsafe/mafr:8.5/')
        ->and($builder->maxResultDimension(20000)->url())->toContain('/unsafe/mrd:20000/');
});

it('appends option segments verbatim in call order', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->width(300)->withOption('cb:abc')->quality(80)->url())
        ->toBe('http://imgproxy.example.com/unsafe/w:300/cb:abc/q:80/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc')
        ->and($builder->resizeWithGravity(300, 400)->withOption('fancy:1')->zoom(2)->url())
        ->toContain('/unsafe/s:300:400/fancy:1/z:2/');
});

it('appends option segments without validation', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    expect($builder->withOption('anything:goes:here')->url())->toContain('/unsafe/anything:goes:here/');
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

    $builder->extend(Gravity::Smart);
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

it('throws when a resize-with-gravity dimension is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->resizeWithGravity(-1, 400);
})->throws(InvalidArgumentException::class);

it('throws when the resize-with-gravity gravity is the smart gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->resizeWithGravity(300, 400, extend: true, gravity: Gravity::Smart);
})->throws(InvalidArgumentException::class);

it('throws when the min width or min height is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->minWidth(-1);
})->throws(InvalidArgumentException::class);

it('throws when a zoom factor is not greater than zero', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->zoom(0);
})->throws(InvalidArgumentException::class);

it('throws when the extend aspect ratio gravity is the smart gravity', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->extendAspectRatio(Gravity::Smart);
})->throws(InvalidArgumentException::class);

it('throws when a focus point offset is outside 0-1', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->focusPoint(2, 0.5);
})->throws(InvalidArgumentException::class);

it('throws when the trim threshold is negative or the color is not a hex value', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->trim(-1);
})->throws(InvalidArgumentException::class);

it('throws when a padding side is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->padding(10, -2);
})->throws(InvalidArgumentException::class);

it('throws when the pixelate size is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->pixelate(-1);
})->throws(InvalidArgumentException::class);

it('throws when a format quality pair has an unknown format or an out-of-range quality', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->formatQuality(['bogus' => 75]);
})->throws(InvalidArgumentException::class);

it('throws when the skip processing format is unknown', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->skipProcessing('bogus');
})->throws(InvalidArgumentException::class);

it('throws when the cache buster or filename is empty', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->cacheBuster('');
})->throws(InvalidArgumentException::class);

it('throws when the expires timestamp is negative', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->expires(-1);
})->throws(InvalidArgumentException::class);

it('throws when the server preset name is empty', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->preset('');
})->throws(InvalidArgumentException::class);

it('throws when a security limit is negative or zero', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->maxSourceResolution(-1);
})->throws(InvalidArgumentException::class);

it('throws when the max animation frames is not greater than zero', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://example.com/image/curiosity.jpg');

    $builder->maxAnimationFrames(0);
})->throws(InvalidArgumentException::class);
