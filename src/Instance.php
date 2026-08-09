<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use InvalidArgumentException;

/**
 * A configured imgproxy instance.
 *
 * Carries the connection details of one imgproxy server: base URL, signing
 * key and salt, signature size, and source encoding. URL building is
 * delegated to the URL builder, seeded with these settings.
 */
final class Instance
{
    /**
     * @param  string  $url  Base URL of the imgproxy server, without trailing slash.
     * @param  string|null  $key  Hex-encoded signing key, or null for unsigned URLs.
     * @param  string|null  $salt  Hex-encoded signing salt, or null for unsigned URLs.
     * @param  int|null  $signatureSize  Signature bytes to keep (1-32), or null for the full 32.
     * @param  string  $encoding  Source encoding: "base64" or "plain".
     *
     * @throws InvalidArgumentException When the base URL is empty.
     */
    public function __construct(
        private readonly string $url,
        private readonly ?string $key = null,
        private readonly ?string $salt = null,
        private readonly ?int $signatureSize = null,
        private readonly string $encoding = 'base64',
    ) {
        if ($url === '') {
            throw new InvalidArgumentException('The imgproxy instance has no URL configured.');
        }
    }

    /**
     * A URL builder for the given source, configured with this instance's
     * connection details.
     */
    public function url(string $source): Builder
    {
        return new Builder($this->url, $source, $this->encoding, [], $this->key, $this->salt, $this->signatureSize);
    }

    /**
     * The base URL of the imgproxy server.
     */
    public function baseUrl(): string
    {
        return $this->url;
    }

    /**
     * The hex-encoded signing key, or null for unsigned URLs.
     */
    public function key(): ?string
    {
        return $this->key;
    }

    /**
     * The hex-encoded signing salt, or null for unsigned URLs.
     */
    public function salt(): ?string
    {
        return $this->salt;
    }

    /**
     * The signature bytes to keep, or null for the full digest.
     */
    public function signatureSize(): ?int
    {
        return $this->signatureSize;
    }

    /**
     * The source encoding: "base64" or "plain".
     */
    public function encoding(): string
    {
        return $this->encoding;
    }
}
