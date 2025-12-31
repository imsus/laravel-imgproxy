<?php

namespace Imsus\ImgProxy;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ImgProxyResponse
{
    protected string $url;

    protected int $status;

    protected array $headers;

    public function __construct(
        string $url,
        int $status = 302,
        array $headers = []
    ) {
        $this->url = $url;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Create an ImgProxyResponse from a URL.
     *
     * @param  string  $url  The ImgProxy URL
     */
    public static function make(string $url): self
    {
        return new self($url);
    }

    /**
     * Create a redirect response to the ImgProxy URL.
     *
     * @param  int  $status  HTTP status code (default 302)
     * @param  array  $additionalHeaders  Additional headers to include
     */
    public function redirect(int $status = 302, array $additionalHeaders = []): RedirectResponse
    {
        return new RedirectResponse($this->url, $status, array_merge(
            ['Cache-Control' => 'no-cache, no-store, must-revalidate'],
            $this->headers,
            $additionalHeaders
        ));
    }

    /**
     * Create a response that streams the URL as text.
     */
    public function stream(): Response
    {
        return new Response($this->url, 200, array_merge($this->headers, [
            'Content-Type' => 'text/plain',
        ]));
    }

    /**
     * Get the URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get the headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
