<?php

declare(strict_types=1);

use Imsus\LaravelImgproxy\Builder;

it('replaces the unsafe slot with the signature', function () {
    // key "secret" (hex 736563726574), salt "hello" (hex 68656C6C6F).
    //
    // Note: the signature printed in the imgproxy docs for this path
    // (oKfUtW34Dvo2BGQehJFR4Nr0_rIjOtdtzJ3QFsUcXH8) is stale and is rejected
    // with HTTP 403 by a real v4 server. The expected value below reproduces
    // the official examples/signature.php algorithm and is accepted by a real
    // v4 imgproxy running with this key/salt pair.
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        segments: ['rs:fill:300:400:0', 'g:sm'],
        key: '736563726574',
        salt: '68656C6C6F',
    );

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/Jn6kyi5kKLc44Okrcpq9aNRTu6XHNExAr2L-K7lln1E/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('signs the plain-encoded path including its percent-encoding', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://img.example.com/pretty/image.jpg',
        'plain',
        segments: ['rs:fit:300:300'],
        key: '943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881',
        salt: '520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5',
    );

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/4fRFUjO56QXKVwV05fJ-512KKdGsC7G3t63uMIRKmWM/rs:fit:300:300/plain/http%3A%2F%2Fimg.example.com%2Fpretty%2Fimage.jpg');
});

it('truncates the signature when signature_size is configured', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        segments: ['rs:fill:300:400:0', 'g:sm'],
        key: '736563726574',
        salt: '68656C6C6F',
        signatureSize: 8,
    );

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/Jn6kyi5kKLc/rs:fill:300:400:0/g:sm/aHR0cDovL2V4YW1wbGUuY29tL2ltYWdlL2N1cmlvc2l0eS5qcGc');
});

it('keeps the unsafe slot when no key is configured', function () {
    $builder = new Builder('http://imgproxy.example.com', 'http://img.example.com/example.jpg');

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2ltZy5leGFtcGxlLmNvbS9leGFtcGxlLmpwZw');
});

it('keeps the unsafe slot when only the key is configured', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://img.example.com/example.jpg',
        key: '736563726574',
    );

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2ltZy5leGFtcGxlLmNvbS9leGFtcGxlLmpwZw');
});

it('keeps the unsafe slot when only the salt is configured', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://img.example.com/example.jpg',
        salt: '68656C6C6F',
    );

    expect($builder->url())
        ->toBe('http://imgproxy.example.com/unsafe/aHR0cDovL2ltZy5leGFtcGxlLmNvbS9leGFtcGxlLmpwZw');
});

it('keeps signing parameters when the encoding changes', function () {
    $builder = new Builder(
        'http://imgproxy.example.com',
        'http://example.com/image/curiosity.jpg',
        segments: ['rs:fill:300:400:0', 'g:sm'],
        key: '736563726574',
        salt: '68656C6C6F',
    );
    $plain = $builder->sourceEncoding('plain');

    expect($plain->url())
        ->toBe('http://imgproxy.example.com/_AEymtF7nplW8S8sP962f94boDnL1uUbln8QrmBiuPA/rs:fill:300:400:0/g:sm/plain/http%3A%2F%2Fexample.com%2Fimage%2Fcuriosity.jpg')
        ->and($builder->url())->toContain('/Jn6kyi5kKLc44Okrcpq9aNRTu6XHNExAr2L-K7lln1E/');
});

it('throws when the configured key is not valid hex', function () {
    new Builder(
        'http://imgproxy.example.com',
        'http://img.example.com/example.jpg',
        key: 'not-hex',
        salt: '68656C6C6F',
    );
})->throws(InvalidArgumentException::class);
