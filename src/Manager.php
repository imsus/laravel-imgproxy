<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Resolves imgproxy instances from the published config file.
 *
 * @phpstan-type InstanceConfig array{url: string|null, key: string|null, salt: string|null, signature_size: int|null, encoding: string}
 */
final class Manager
{
    /**
     * @param  array{default: string, instances: array<string, InstanceConfig>, presets: array<string, array<string, mixed>>}  $config
     */
    public function __construct(private readonly array $config) {}

    /**
     * The name of the default imgproxy instance.
     */
    public function defaultInstance(): string
    {
        return $this->config['default'];
    }

    /**
     * Resolve an imgproxy instance.
     *
     * @throws InvalidArgumentException When the instance is not configured or has no base URL.
     */
    public function instance(?string $name = null): Instance
    {
        $name ??= $this->config['default'];

        $instances = $this->config['instances'];

        if (! array_key_exists($name, $instances)) {
            throw new InvalidArgumentException("The imgproxy instance [{$name}] is not configured.");
        }

        $config = $instances[$name];

        return new Instance(
            $config['url'] ?? '',
            $config['key'] ?? null,
            $config['salt'] ?? null,
            $config['signature_size'] ?? null,
            $config['encoding'],
            $this->config['presets'],
        );
    }

    /**
     * Build a URL for the given source on the default (or named) instance.
     *
     * @throws InvalidArgumentException When the instance is not configured or has no base URL.
     */
    public function image(string $source, ?string $instance = null): Builder
    {
        return $this->instance($instance)->image($source);
    }

    /**
     * Build a URL for a source on a Storage disk.
     *
     * Mirrors the Storage macro: public disks use the disk's url(), private
     * disks (those providing temporary URLs without explicit public
     * visibility) use a pre-signed temporaryUrl() with the default lifetime.
     *
     * @throws InvalidArgumentException When the disk is not configured.
     */
    public function fromStorage(string $path, string $disk, ?string $instance = null): Builder
    {
        return $this->image(DiskUrl::resolve(Storage::disk($disk), $path), $instance);
    }

    /**
     * Build a URL for a source given as a raw path.
     *
     * @throws InvalidArgumentException When the instance is not configured or has no base URL.
     */
    public function fromPath(string $path, ?string $instance = null): Builder
    {
        return $this->image($path, $instance);
    }

    /**
     * Build a URL for a source given as a URL.
     *
     * @throws InvalidArgumentException When the instance is not configured or has no base URL.
     */
    public function fromUrl(string $url, ?string $instance = null): Builder
    {
        return $this->image($url, $instance);
    }
}
