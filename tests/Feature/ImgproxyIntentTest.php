<?php

declare(strict_types=1);

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\Exceptions\ImgproxyStorageException;
use Imsus\LaravelImgproxy\Imgproxy;
use Imsus\LaravelImgproxy\StoredImage;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

afterEach(function () {
    Carbon::setTestNow();

    if (Storage::disk('public')->exists('processed')) {
        Storage::disk('public')->deleteDirectory('processed');
    }

    if (config('filesystems.disks.recording') !== null) {
        Storage::disk('recording')->delete('processed/photo.webp');
    }
});

beforeEach(function () {
    config()->set('laravel-imgproxy', [
        'default' => 'default',
        'instances' => [
            'default' => [
                'url' => 'https://imgproxy.example.com',
                'key' => null,
                'salt' => null,
                'signature_size' => null,
                'encoding' => 'base64',
            ],
        ],
        'presets' => [],
    ]);

    config()->set('filesystems.disks.public.url', 'https://cdn.example.com');
});

it('stores a processed image publicly with the visibility option', function () {
    Storage::extend('recording', function ($app, array $config) {
        $adapter = new LocalFilesystemAdapter($config['root']);

        return new IntentRecordingFilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    });

    config()->set('filesystems.disks.recording', [
        'driver' => 'recording',
        'root' => storage_path('app/recording'),
    ]);

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->cover(400, 400)
        ->storePublicly('recording', 'processed/photo.webp');

    expect($image)->toBeInstanceOf(StoredImage::class)
        ->and($image->disk())->toBe('recording')
        ->and($image->path())->toBe('processed/photo.webp')
        ->and(Storage::disk('recording')->get('processed/photo.webp'))->toBe('image-bytes')
        ->and(Storage::disk('recording')->lastWriteOptions)->toBe(['visibility' => 'public']);

    Http::assertSent(fn (Request $request) => $request->url() === 'https://imgproxy.example.com/unsafe/rs:fill:400:400/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('forwards extra options to the disk write and keeps public visibility', function () {
    Storage::extend('recording', function ($app, array $config) {
        $adapter = new LocalFilesystemAdapter($config['root']);

        return new IntentRecordingFilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    });

    config()->set('filesystems.disks.recording', [
        'driver' => 'recording',
        'root' => storage_path('app/recording'),
    ]);

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->storePublicly('recording', 'processed/photo.webp', ['mime_type' => 'image/webp']);

    expect(Storage::disk('recording')->lastWriteOptions)->toBe([
        'mime_type' => 'image/webp',
        'visibility' => 'public',
    ]);
});

it('throws without writing when storePublicly hits a non-2xx response', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('Not Found', 404),
    ]);

    expect(fn () => imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->storePublicly('public', 'processed/photo.webp'))
        ->toThrow(ImgproxyStorageException::class, 'HTTP 404')
        ->and(Storage::disk('public')->exists('processed/photo.webp'))->toBeFalse();
});

it('builds a URL from a public disk through fromStorage', function () {
    $builder = Imgproxy::fromStorage('images/photo.jpg', 'public');

    expect($builder)->toBeInstanceOf(Builder::class)
        ->and($builder->url())->toBe(Storage::disk('public')->imgproxy('images/photo.jpg')->url());
});

it('builds a pre-signed URL from a private disk through fromStorage', function () {
    Carbon::setTestNow('2026-01-01 00:00:00+00:00');

    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, $expiration, array $options = []): string => 'https://signed.example.com/'.$path.'?expires='.$expiration->getTimestamp(),
    );

    expect(Imgproxy::fromStorage('images/photo.jpg', 'local')->url())
        ->toBe(Storage::disk('local')->imgproxy('images/photo.jpg')->url());
});

it('builds a URL from a named instance through fromStorage', function () {
    config()->set('laravel-imgproxy.instances.staging', [
        'url' => 'https://imgproxy.staging.example.com',
        'key' => null,
        'salt' => null,
        'signature_size' => null,
        'encoding' => 'base64',
    ]);

    $defaultSource = Storage::disk('public')->imgproxy('images/photo.jpg')->url();

    expect(Imgproxy::fromStorage('images/photo.jpg', 'public', 'staging')->url())
        ->toBe('https://imgproxy.staging.example.com'.str_replace('https://imgproxy.example.com', '', $defaultSource));
});

it('delegates fromPath to image', function () {
    expect(Imgproxy::fromPath('https://example.com/image.jpg'))->toBeInstanceOf(Builder::class)
        ->and(Imgproxy::fromPath('https://example.com/image.jpg')->url())
        ->toBe(Imgproxy::image('https://example.com/image.jpg')->url());
});

it('delegates fromUrl to image', function () {
    expect(Imgproxy::fromUrl('https://example.com/image.jpg'))->toBeInstanceOf(Builder::class)
        ->and(Imgproxy::fromUrl('https://example.com/image.jpg')->url())
        ->toBe(Imgproxy::image('https://example.com/image.jpg')->url());
});

it('throws when the fromStorage disk is not configured', function () {
    Imgproxy::fromStorage('images/photo.jpg', 'missing');
})->throws(InvalidArgumentException::class, 'Disk [missing]');

/**
 * A disk adapter that records the options of the last write, for asserting
 * that storePublicly forwards its options to the disk write.
 */
final class IntentRecordingFilesystemAdapter extends FilesystemAdapter
{
    /**
     * @var array<string, mixed>
     */
    public array $lastWriteOptions = [];

    /**
     * @param  array<string, mixed>  $options
     */
    public function writeStream($path, $resource, array $options = []): bool
    {
        $this->lastWriteOptions = $options;

        return parent::writeStream($path, $resource, $options);
    }
}
