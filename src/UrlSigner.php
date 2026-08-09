<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy;

use InvalidArgumentException;

/**
 * Computes imgproxy URL signatures (HMAC-SHA256, URL-safe base64, no padding).
 *
 * The key and salt are hex-encoded strings as configured for the imgproxy
 * server (IMGPROXY_KEY / IMGPROXY_SALT). The signature covers the request
 * path after the signature slot, leading slash included, exactly as imgproxy
 * verifies it. `signature_size` truncates the digest to match the server's
 * IMGPROXY_SIGNATURE_SIZE; sizes of 32 or more keep the full digest.
 *
 * An empty key or salt disables signing (empty signature), mirroring imgproxy:
 * when no key/salt pair is configured the server does not verify signatures.
 */
final class UrlSigner
{
    private readonly ?string $keyBin;

    private readonly ?string $saltBin;

    /**
     * @param  string|null  $key  Hex-encoded key, or null/empty for unsigned URLs.
     * @param  string|null  $salt  Hex-encoded salt, or null/empty for unsigned URLs.
     * @param  int|null  $signatureSize  Signature bytes to keep (1-32), or null for the full 32.
     *
     * @throws InvalidArgumentException When the key or salt is not valid hex, or the signature size is out of range.
     */
    public function __construct(
        private readonly ?string $key = null,
        private readonly ?string $salt = null,
        private readonly ?int $signatureSize = null,
    ) {
        $this->keyBin = $this->decodeHex($key, 'key');
        $this->saltBin = $this->decodeHex($salt, 'salt');
        $this->validateSignatureSize($signatureSize);
    }

    /**
     * The URL-safe base64 signature of the given path, or an empty string when
     * signing is disabled.
     */
    public function sign(string $path): string
    {
        if ($this->keyBin === null || $this->saltBin === null) {
            return '';
        }

        // imgproxy re-adds the leading slash if a proxy stripped it.
        if ($path !== '' && $path[0] !== '/') {
            $path = '/'.$path;
        }

        $digest = hash_hmac('sha256', $this->saltBin.$path, $this->keyBin, true);
        $size = $this->signatureSize ?? 32;

        if ($size < 32) {
            $digest = substr($digest, 0, $size);
        }

        return rtrim(strtr(base64_encode($digest), '+/', '-_'), '=');
    }

    /**
     * The configured hex-encoded key.
     */
    public function key(): ?string
    {
        return $this->key;
    }

    /**
     * The configured hex-encoded salt.
     */
    public function salt(): ?string
    {
        return $this->salt;
    }

    /**
     * The configured signature size in bytes, or null for the full digest.
     */
    public function signatureSize(): ?int
    {
        return $this->signatureSize;
    }

    /**
     * @throws InvalidArgumentException When the string is not valid hex.
     */
    private function decodeHex(?string $hex, string $name): ?string
    {
        if ($hex === null || $hex === '') {
            return null;
        }

        if (preg_match('/\A[0-9a-fA-F]+\z/', $hex) !== 1 || strlen($hex) % 2 !== 0) {
            throw new InvalidArgumentException("The imgproxy signing {$name} must be a hex-encoded string.");
        }

        $decoded = hex2bin($hex);

        if ($decoded === false) {
            throw new InvalidArgumentException("The imgproxy signing {$name} must be a hex-encoded string.");
        }

        return $decoded;
    }

    /**
     * @throws InvalidArgumentException When the signature size is outside 1-32.
     */
    private function validateSignatureSize(?int $size): void
    {
        if ($size !== null && ($size < 1 || $size > 32)) {
            throw new InvalidArgumentException('The imgproxy signature size must be an integer between 1 and 32, or null.');
        }
    }
}
