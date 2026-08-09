<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use InvalidArgumentException;

/**
 * Resolves imgproxy instance configuration from the published config file.
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
     * Resolve the configuration of an imgproxy instance.
     *
     *
     * @return InstanceConfig
     *
     * @throws InvalidArgumentException When the instance is not configured.
     */
    public function instance(?string $name = null): array
    {
        $name ??= $this->config['default'];

        $instances = $this->config['instances'];

        if (! array_key_exists($name, $instances)) {
            throw new InvalidArgumentException("The imgproxy instance [{$name}] is not configured.");
        }

        return $instances[$name];
    }
}
