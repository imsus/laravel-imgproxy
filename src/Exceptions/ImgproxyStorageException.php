<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;
use Throwable;

/**
 * Thrown when a processed image cannot be fetched from imgproxy or written
 * to a destination disk.
 */
final class ImgproxyStorageException extends RuntimeException
{
    /**
     * The exception for a non-successful imgproxy response. The message
     * carries the HTTP status, the requested URL, and a snippet of the body.
     */
    public static function fromResponse(string $url, Response $response): self
    {
        $body = mb_substr((string) $response->body(), 0, 200);

        return new self("imgproxy responded with HTTP {$response->status()} for [{$url}]: {$body}");
    }

    /**
     * The exception for a failed write to the destination disk, preserving
     * the original write error when one was raised (disks configured with
     * "throw" => false report failures by returning false instead).
     */
    public static function fromWrite(string $disk, string $path, ?Throwable $previous = null): self
    {
        $reason = $previous?->getMessage() ?? 'the disk did not report an error';

        return new self(
            "Failed to store the processed image on disk [{$disk}] at [{$path}]: {$reason}",
            0,
            $previous,
        );
    }
}
