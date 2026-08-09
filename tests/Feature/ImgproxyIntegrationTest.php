<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Builder;
use LaravelImgproxy\LaravelImgproxy\Enums\Format;
use LaravelImgproxy\LaravelImgproxy\Enums\Gravity;
use LaravelImgproxy\LaravelImgproxy\Enums\ResizeType;

/**
 * Live checks against a real imgproxy (local only).
 *
 * The developer's local imgproxy runs in Docker at http://localhost:8081 with
 * URL signature checking enabled and IMGPROXY_KEY / IMGPROXY_SALT set to 64
 * hex zeros. Export the same three variables in the shell that runs the test
 * suite to enable these tests:
 *
 *   IMGPROXY_URL=http://localhost:8081
 *   IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000
 *   IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000
 *
 * CI does not set these variables, so the tests skip there.
 */

/**
 * The live imgproxy environment, or null when not configured.
 *
 * @return array{url: string, key: string, salt: string}|null
 */
$liveImgproxy = function (): ?array {
    $url = getenv('IMGPROXY_URL');
    $key = getenv('IMGPROXY_KEY');
    $salt = getenv('IMGPROXY_SALT');

    if (! is_string($url) || ! is_string($key) || ! is_string($salt) || $url === '' || $key === '' || $salt === '') {
        return null;
    }

    return ['url' => $url, 'key' => $key, 'salt' => $salt];
};

/**
 * Fetch a URL and return its HTTP status and content type.
 *
 * @return array{status: int, contentType: string|null}
 */
$fetch = function (string $url): array {
    $context = stream_context_create([
        'http' => [
            'timeout' => 20,
            'ignore_errors' => true,
        ],
    ]);

    @file_get_contents($url, false, $context);

    $status = 0;
    $contentType = null;

    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/\AHTTP\/\S+\s+(\d{3})/', $header, $matches) === 1) {
            $status = (int) $matches[1];
        }

        if (str_starts_with(strtolower($header), 'content-type:')) {
            $contentType = trim(substr($header, strlen('content-type:')));
        }
    }

    return ['status' => $status, 'contentType' => $contentType];
};

it('serves a generated signed URL from a real imgproxy', function () use ($liveImgproxy, $fetch) {
    $env = $liveImgproxy();

    if ($env === null) {
        $this->markTestSkipped('Set IMGPROXY_URL, IMGPROXY_KEY, and IMGPROXY_SALT to run the live imgproxy check (local Docker imgproxy only).');
    }

    $source = 'https://raw.githubusercontent.com/imgproxy/imgproxy/master/testdata/test1.png';
    $signed = new Builder($env['url'], $source, segments: ['rs:fit:300:300'], key: $env['key'], salt: $env['salt']);
    $unsigned = new Builder($env['url'], $source, segments: ['rs:fit:300:300']);

    $signedResponse = $fetch($signed->url());
    $unsignedResponse = $fetch($unsigned->url());

    expect($signedResponse['status'])->toBe(200)
        ->and($signedResponse['contentType'])->toStartWith('image/')
        ->and($unsignedResponse['status'])->not->toBe(200);
});

it('serves a signed URL composed by the typed fluent API from a real imgproxy', function () use ($liveImgproxy, $fetch) {
    $env = $liveImgproxy();

    if ($env === null) {
        $this->markTestSkipped('Set IMGPROXY_URL, IMGPROXY_KEY, and IMGPROXY_SALT to run the live imgproxy check (local Docker imgproxy only).');
    }

    $source = 'https://raw.githubusercontent.com/imgproxy/imgproxy/master/testdata/test1.png';
    $builder = (new Builder($env['url'], $source, key: $env['key'], salt: $env['salt']))
        ->resize(ResizeType::Fill, 300, 400)
        ->gravity(Gravity::Smart)
        ->quality(80)
        ->format(Format::Webp);

    $response = $fetch($builder->url());

    expect($response['status'])->toBe(200)
        ->and($response['contentType'])->toBe('image/webp');
});
