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

    /**
     * @param  list<string>  $segments  Processing option segments, in call order.
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $source,
        private readonly string $encoding = 'base64',
        private readonly array $segments = [],
    ) {
        $this->validateEncoding($encoding);
    }

    /**
     * The full imgproxy URL.
     */
    public function url(): string
    {
        $path = '/unsafe';

        if ($this->segments !== []) {
            $path .= '/'.implode('/', $this->segments);
        }

        return rtrim($this->baseUrl, '/').$path.'/'.$this->encodedSource();
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
        return new self($this->baseUrl, $this->source, $encoding, $this->segments);
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
