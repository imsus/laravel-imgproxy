<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\UrlSigner;

it('reproduces the official imgproxy PHP example byte for byte', function () {
    $signer = new UrlSigner(
        key: '943b421c9eb07c830af81030552c86009268de4e532ba2ee2eab8247c6da0881',
        salt: '520f986b998545b4785e0defbc4f3c1203f22de2374a3d53cb7a7fe9fea309c5',
    );

    $path = '/rs:fit:300:300/plain/http://img.example.com/pretty/image.jpg';

    expect($signer->sign($path))
        ->toBe('m3k5QADfcKPDj-SDI2AIogZbC3FlAXszuwhtWXYqavc')
        ->and('/'.$signer->sign($path).$path)
        ->toBe('/m3k5QADfcKPDj-SDI2AIogZbC3FlAXszuwhtWXYqavc/rs:fit:300:300/plain/http://img.example.com/pretty/image.jpg');
});

it('matches the imgproxy Go test vector for test-key and test-salt', function () {
    $signer = new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74');

    expect($signer->sign('asd'))->toBe('oWaL7QoW5TsgbuiS9-5-DI8S3Ibbo1gdB2SteJh3a20');
});

it('truncates the signature to signature_size bytes', function () {
    $signer = new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74', signatureSize: 8);

    expect($signer->sign('asd'))->toBe('oWaL7QoW5Ts');
});

it('signs the path as given, re-adding a missing leading slash', function () {
    $signer = new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74');

    expect($signer->sign('/asd'))->toBe($signer->sign('asd'));
});

it('returns an empty signature when the key is empty', function () {
    expect((new UrlSigner(key: '', salt: '746573742d73616c74'))->sign('/asd'))->toBe('')
        ->and((new UrlSigner(salt: '746573742d73616c74'))->sign('/asd'))->toBe('');
});

it('returns an empty signature when the salt is empty', function () {
    expect((new UrlSigner(key: '746573742d6b6579', salt: ''))->sign('/asd'))->toBe('')
        ->and((new UrlSigner(key: '746573742d6b6579'))->sign('/asd'))->toBe('');
});

it('accepts uppercase hex key and salt', function () {
    $signer = new UrlSigner(key: '746573742D6B6579', salt: '746573742D73616C74');

    expect($signer->sign('/asd'))->toBe('oWaL7QoW5TsgbuiS9-5-DI8S3Ibbo1gdB2SteJh3a20');
});

it('throws when the key is not valid hex', function () {
    new UrlSigner(key: 'not-hex', salt: '746573742d73616c74');
})->throws(InvalidArgumentException::class);

it('throws when the key hex has an odd length', function () {
    new UrlSigner(key: 'abc', salt: '746573742d73616c74');
})->throws(InvalidArgumentException::class);

it('throws when the salt is not valid hex', function () {
    new UrlSigner(key: '746573742d6b6579', salt: 'not-hex');
})->throws(InvalidArgumentException::class);

it('throws when the signature size is below one', function () {
    new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74', signatureSize: 0);
})->throws(InvalidArgumentException::class);

it('throws when the signature size exceeds 32', function () {
    new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74', signatureSize: 33);
})->throws(InvalidArgumentException::class);

it('accepts a signature size of 32 as the full signature', function () {
    $signer = new UrlSigner(key: '746573742d6b6579', salt: '746573742d73616c74', signatureSize: 32);

    expect($signer->sign('/asd'))->toBe('oWaL7QoW5TsgbuiS9-5-DI8S3Ibbo1gdB2SteJh3a20');
});

it('exposes the configured key, salt, and signature size', function () {
    $signer = new UrlSigner(key: '736563726574', salt: '68656C6C6F', signatureSize: 8);

    expect($signer->key())->toBe('736563726574')
        ->and($signer->salt())->toBe('68656C6C6F')
        ->and($signer->signatureSize())->toBe(8);
});
