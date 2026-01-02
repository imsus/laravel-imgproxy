<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Illuminate\Filesystem\FilesystemAdapter;
use Imsus\ImgProxy\ImgProxy;
use Mockery;

describe('ImgProxy::fromStorage()', function () {
    it('creates imgproxy instance from public disk path', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('url')
            ->with('avatars/user.jpg')
            ->andReturn('https://cdn.example.com/storage/avatars/user.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'avatars/user.jpg')
            ->width(300)
            ->height(200)
            ->build();

        expect($url)->toContain('width:300');
        expect($url)->toContain('height:200');
        // The source URL is base64 encoded, check for extension
        expect($url)->toContain('.jpg');

        Mockery::close();
    });

    it('works with fluent api', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('url')
            ->with('images/photo.jpg')
            ->andReturn('https://cdn.example.com/images/photo.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'images/photo.jpg')
            ->width(500)
            ->height(300)
            ->webp()
            ->build();

        expect($url)->toContain('width:500');
        expect($url)->toContain('height:300');
        expect($url)->toContain('.webp');

        Mockery::close();
    });

    it('can chain all imgproxy methods', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('url')
            ->with('test.jpg')
            ->andReturn('https://cdn.example.com/test.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'test.jpg')
            ->width(100)
            ->height(100)
            ->cover()
            ->quality(85)
            ->build();

        expect($url)->toContain('width:100');
        expect($url)->toContain('height:100');
        expect($url)->toContain('resizing_type:fill');
        expect($url)->toContain('quality:85');

        Mockery::close();
    });

    it('uses temporaryUrl for private disks', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('temporaryUrl')
            ->with('private/file.jpg')
            ->andReturn('https://s3.amazonaws.com/bucket/private/file.jpg?signature=abc')
            ->getMock()
            ->shouldReceive('url')
            ->with('private/file.jpg')
            ->andReturn('https://s3.amazonaws.com/bucket/private/file.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'private/file.jpg')
            ->width(100)
            ->build();

        expect($url)->toContain('width:100');
        expect($url)->toContain('.jpg');

        Mockery::close();
    });

    it('falls back to url when temporaryUrl throws exception', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('temporaryUrl')
            ->with('fallback.jpg')
            ->andThrow(new \Exception('Not supported'))
            ->getMock()
            ->shouldReceive('url')
            ->with('fallback.jpg')
            ->andReturn('https://fallback.example.com/fallback.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'fallback.jpg')
            ->width(200)
            ->build();

        expect($url)->toContain('width:200');
        expect($url)->toContain('.jpg');

        Mockery::close();
    });
});
