<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use InvalidArgumentException;

/**
 * Immutable fluent builder for imgproxy URLs.
 *
 * Every mutation returns a new instance, so a base builder can be reused
 * for several URL variants. The source and the base URL are fixed at
 * construction; processing option segments are appended in call order.
 */
final class Builder
{
    /** @var list<string> */
    private const array ENCODINGS = ['base64', 'plain'];

    private readonly UrlSigner $signer;

    /**
     * @param  list<string>  $segments  Processing option segments, in call order.
     * @param  string|null  $key  Hex-encoded signing key, or null for unsigned URLs.
     * @param  string|null  $salt  Hex-encoded signing salt, or null for unsigned URLs.
     * @param  int|null  $signatureSize  Signature bytes to keep (1-32), or null for the full 32.
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $source,
        private readonly string $encoding = 'base64',
        private readonly array $segments = [],
        ?string $key = null,
        ?string $salt = null,
        ?int $signatureSize = null,
    ) {
        $this->validateEncoding($encoding);
        $this->signer = new UrlSigner($key, $salt, $signatureSize);
    }

    /**
     * The full imgproxy URL.
     */
    public function url(): string
    {
        $path = '/'.($this->segments === [] ? '' : implode('/', $this->segments).'/').$this->encodedSource();

        $signature = $this->signer->sign($path) ?: 'unsafe';

        return rtrim($this->baseUrl, '/').'/'.$signature.$path;
    }

    /**
     * The full imgproxy URL.
     */
    public function __toString(): string
    {
        return $this->url();
    }

    /**
     * Return a copy of the builder with a different source encoding.
     *
     * @throws InvalidArgumentException When the encoding is not supported.
     */
    public function encoding(string $encoding): self
    {
        return new self(
            $this->baseUrl,
            $this->source,
            $encoding,
            $this->segments,
            $this->signer->key(),
            $this->signer->salt(),
            $this->signer->signatureSize(),
        );
    }

    private function encodedSource(): string
    {
        if ($this->encoding === 'plain') {
            return 'plain/'.rawurlencode($this->source);
        }

        return rtrim(strtr(base64_encode($this->source), '+/', '-_'), '=');
    }

    private function validateEncoding(string $encoding): void
    {
        if (! in_array($encoding, self::ENCODINGS, true)) {
            throw new InvalidArgumentException("Unsupported imgproxy source encoding [{$encoding}].");
        }
    }
}
