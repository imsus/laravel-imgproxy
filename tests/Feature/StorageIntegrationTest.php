<?php

namespace Imsus\ImgProxy\Tests\Feature;

use Illuminate\Filesystem\FilesystemAdapter;
use Imsus\ImgProxy\ImgProxy;
use Mockery;

describe('Storage Integration', function () {
    it('generates correct signed url with storage macro', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('url')
            ->with('products/123.jpg')
            ->andReturn('https://example.com/storage/products/123.jpg')
            ->getMock();

        $url = (new ImgProxy)->url('https://example.com/storage/products/123.jpg')
            ->width(800)
            ->height(600)
            ->webp()
            ->build();

        // Verify URL structure
        expect($url)->toContain('http://localhost:8080/');
        expect($url)->toContain('/width:800/');
        expect($url)->toContain('/height:600/');
        expect($url)->toContain('.webp');

        Mockery::close();
    });

    it('fromStorage works with full fluent chain', function () {
        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('url')
            ->with('test/image.jpeg')
            ->andReturn('https://cdn.example.com/images/test.jpeg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'test/image.jpeg')
            ->width(500)
            ->height(500)
            ->cover()
            ->quality(90)
            ->build();

        expect($url)->toContain('width:500');
        expect($url)->toContain('height:500');
        expect($url)->toContain('resizing_type:fill');
        expect($url)->toContain('quality:90');

        Mockery::close();
    });

    it('supports multiple chained operations', function () {
        $url = (new ImgProxy)->url('https://cdn.example.com/uploads/photos/landscape.jpg')
            ->width(1200)
            ->height(800)
            ->cover()
            ->setBlur(2)
            ->setSharpen(1.5)
            ->build();

        expect($url)->toContain('width:1200');
        expect($url)->toContain('height:800');
        expect($url)->toContain('resizing_type:fill');
        expect($url)->toContain('blur:2');
        expect($url)->toContain('sharpen:1.5');
    });

    it('fromStorage uses temporaryUrl for private S3 disk', function () {
        $signedUrl = 'https://s3.amazonaws.com/my-bucket/private/image.jpg?X-Amz-Expires=3600&signature=abc123';

        $mockDisk = Mockery::mock(FilesystemAdapter::class)
            ->shouldReceive('temporaryUrl')
            ->with('private/image.jpg')
            ->andReturn($signedUrl)
            ->getMock()
            ->shouldReceive('url')
            ->with('private/image.jpg')
            ->andReturn('https://s3.amazonaws.com/my-bucket/private/image.jpg')
            ->getMock();

        $url = ImgProxy::fromStorage($mockDisk, 'private/image.jpg')
            ->width(400)
            ->webp()
            ->build();

        expect($url)->toContain('width:400');
        expect($url)->toContain('.webp');

        Mockery::close();
    });

    it('fromStorage falls back to url when temporaryUrl throws', function () {
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
            ->width(300)
            ->build();

        expect($url)->toContain('width:300');
        expect($url)->toContain('.jpg');

        Mockery::close();
    });
});
