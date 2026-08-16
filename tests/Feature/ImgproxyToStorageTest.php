<?php

declare(strict_types=1);

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Imsus\LaravelImgproxy\Exceptions\ImgproxyStorageException;
use Imsus\LaravelImgproxy\StoredImage;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

afterEach(function () {
    Carbon::setTestNow();

    Storage::disk('public')->deleteDirectory('processed');
    Storage::disk('public')->delete('blocked');
    Storage::disk('local')->deleteDirectory('processed');

    if (config('filesystems.disks.quiet') !== null) {
        Storage::disk('quiet')->delete('blocked');
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

it('fetches the processed image and stores it on a public disk', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->width(300)
        ->toStorage('public', 'processed/photo.webp');

    expect($image)->toBeInstanceOf(StoredImage::class)
        ->and($image->disk())->toBe('public')
        ->and($image->path())->toBe('processed/photo.webp')
        ->and($image->name())->toBe('photo.webp')
        ->and($image->adapter())->toBe(Storage::disk('public'))
        ->and(Storage::disk('public')->get('processed/photo.webp'))->toBe('image-bytes');

    Http::assertSent(fn (Request $request) => $request->url() === 'https://imgproxy.example.com/unsafe/w:300/aHR0cHM6Ly9jZG4uZXhhbXBsZS5jb20vaW1hZ2VzL3Bob3RvLmpwZw');
});

it('extracts the file name from a nested destination path', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'processed/2026/photo.webp');

    expect($image->name())->toBe('photo.webp')
        ->and($image->url())->toBe('https://cdn.example.com/processed/2026/photo.webp');
});

it('overwrites an existing file at the destination path', function () {
    Storage::disk('public')->put('processed/photo.webp', 'old-bytes');

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('new-bytes', 200),
    ]);

    imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'processed/photo.webp');

    expect(Storage::disk('public')->get('processed/photo.webp'))->toBe('new-bytes');
});

it('throws without writing when imgproxy responds with a non-success status', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('Not Found', 404),
    ]);

    expect(fn () => imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'processed/photo.webp'))
        ->toThrow(ImgproxyStorageException::class, 'HTTP 404')
        ->and(Storage::disk('public')->exists('processed/photo.webp'))->toBeFalse();
});

it('wraps a failed disk write in the storage exception', function () {
    Storage::disk('public')->put('blocked', 'a file blocking the directory');

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    expect(fn () => imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'blocked/photo.webp'))
        ->toThrow(ImgproxyStorageException::class, 'public');
});

it('throws when the write fails on a disk that reports failures instead of throwing', function () {
    config()->set('filesystems.disks.quiet', [
        'driver' => 'local',
        'root' => storage_path('app/quiet'),
        'throw' => false,
    ]);

    Storage::disk('quiet')->put('blocked', 'a file blocking the directory');

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    expect(fn () => imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('quiet', 'blocked/photo.webp'))
        ->toThrow(ImgproxyStorageException::class, 'quiet');
});

it('throws without writing when imgproxy responds with a redirect', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('Moved Permanently', 301),
    ]);

    expect(fn () => imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'processed/photo.webp'))
        ->toThrow(ImgproxyStorageException::class, 'HTTP 301')
        ->and(Storage::disk('public')->exists('processed/photo.webp'))->toBeFalse();
});

it('forwards extra write options to the disk write', function () {
    Storage::extend('recording', function ($app, array $config) {
        $adapter = new LocalFilesystemAdapter($config['root']);

        return new RecordingFilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    });

    config()->set('filesystems.disks.recording', [
        'driver' => 'recording',
        'root' => storage_path('app/recording'),
    ]);

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('recording', 'processed/photo.webp', ['visibility' => 'public']);

    expect(Storage::disk('recording')->lastWriteOptions)->toBe(['visibility' => 'public']);
});

it('throws when the destination disk is not configured', function () {
    imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('missing', 'processed/photo.webp');
})->throws(InvalidArgumentException::class, 'Disk [missing]');

it('yields a plain url on a public destination disk', function () {
    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('public', 'processed/photo.webp');

    expect($image->url())->toBe('https://cdn.example.com/processed/photo.webp')
        ->and((string) $image)->toBe('https://cdn.example.com/processed/photo.webp');
});

it('yields a pre-signed url with the given expiration on a private destination disk', function () {
    Carbon::setTestNow('2026-01-01 00:00:00+00:00');

    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, $expiration, array $options = []): string => 'https://signed.example.com/'.$path.'?expires='.$expiration->getTimestamp(),
    );

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('local', 'processed/photo.webp');

    expect($image->url(3600))->toBe('https://signed.example.com/processed/photo.webp?expires=1767229200');
});

it('defaults a private destination url to a five minute expiration', function () {
    Carbon::setTestNow('2026-01-01 00:00:00+00:00');

    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, $expiration, array $options = []): string => 'https://signed.example.com/'.$path.'?expires='.$expiration->getTimestamp(),
    );

    Http::fake([
        'https://imgproxy.example.com/*' => Http::response('image-bytes', 200),
    ]);

    $image = imgproxy()->image('https://cdn.example.com/images/photo.jpg')
        ->toStorage('local', 'processed/photo.webp');

    expect($image->url())->toBe('https://signed.example.com/processed/photo.webp?expires=1767225900');
});

/**
 * A disk adapter that records the options of the last write, for asserting
 * that toStorage forwards its options to the disk write.
 */
final class RecordingFilesystemAdapter extends FilesystemAdapter
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
