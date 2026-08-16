<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy;

use DateTimeInterface;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * A processed image that has been fetched from imgproxy and written to a
 * destination disk by Builder::toStorage().
 *
 * It is immutable and holds only the disk name and path, so it is
 * serializable and cheap to pass around; the disk adapter and URLs
 * resolve lazily.
 */
final class StoredImage
{
    public function __construct(
        private readonly string $disk,
        private readonly string $path,
    ) {}

    /**
     * The name of the destination disk.
     */
    public function disk(): string
    {
        return $this->disk;
    }

    /**
     * The path of the stored image on the destination disk.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * The file name of the stored image.
     */
    public function name(): string
    {
        return basename($this->path);
    }

    /**
     * The destination disk adapter.
     */
    public function adapter(): FilesystemAdapter
    {
        return Storage::disk($this->disk);
    }

    /**
     * A URL for the stored image: the plain object URL on public disks, a
     * pre-signed temporary URL on private disks.
     *
     * @param  int|DateTimeInterface|null  $expiration  Temporary URL lifetime in seconds from now, an absolute time, or null for the default 5 minutes.
     */
    public function url(int|DateTimeInterface|null $expiration = null): string
    {
        return DiskUrl::resolve($this->adapter(), $this->path, $expiration);
    }

    /**
     * The stored image URL, for use in Blade and string contexts.
     */
    public function __toString(): string
    {
        return $this->url();
    }
}
