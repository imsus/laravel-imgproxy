<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy;

use DateTimeInterface;
use Illuminate\Filesystem\FilesystemAdapter;

/**
 * Resolves a Storage disk path to a URL: the plain url() on public disks, a
 * pre-signed temporaryUrl() on private ones. Shared by imgproxy source
 * resolution and stored-image URL resolution.
 *
 * A disk is treated as private when its driver can produce temporary URLs
 * and its config does not mark it public.
 */
final class DiskUrl
{
    /**
     * The default lifetime of temporary URLs, when no expiration is given.
     */
    private const int DEFAULT_EXPIRATION_SECONDS = 300;

    /**
     * Resolve a disk path to a URL, pre-signing it on private disks.
     *
     * @param  int|DateTimeInterface|null  $expiration  Temporary URL lifetime in seconds from now, an absolute time, or null for the default 5 minutes.
     */
    public static function resolve(FilesystemAdapter $disk, string $path, int|DateTimeInterface|null $expiration = null): string
    {
        $temporary = $disk->providesTemporaryUrls()
            && ($disk->getConfig()['visibility'] ?? null) !== 'public';

        if (! $temporary) {
            return $disk->url($path);
        }

        return $disk->temporaryUrl($path, self::expiration($expiration));
    }

    private static function expiration(int|DateTimeInterface|null $expiration): DateTimeInterface
    {
        if ($expiration instanceof DateTimeInterface) {
            return $expiration;
        }

        return now()->addSeconds($expiration ?? self::DEFAULT_EXPIRATION_SECONDS);
    }
}
