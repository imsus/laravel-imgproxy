<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy;

use Illuminate\Support\Facades\Facade;
use InvalidArgumentException;

/**
 * Proxies to the imgproxy manager.
 *
 * Not final so applications can mock it in tests with the standard facade
 * pattern (Imgproxy::shouldReceive(...)).
 *
 * @method static Builder image(string $source, ?string $instance = null) Build a URL for the given source on the default (or named) instance.
 * @method static Builder fromStorage(string $path, string $disk, ?string $instance = null) Build a URL for a Storage disk-path source on the default (or named) instance.
 * @method static Builder fromPath(string $path, ?string $instance = null) Build a URL for a path source on the default (or named) instance.
 * @method static Builder fromUrl(string $url, ?string $instance = null) Build a URL for a URL source on the default (or named) instance.
 * @method static Instance instance(?string $name = null) Resolve an imgproxy instance.
 * @method static string defaultInstance() The name of the default imgproxy instance.
 *
 * @throws InvalidArgumentException When the instance is not configured or has no base URL.
 */
class Imgproxy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
