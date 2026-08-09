<?php

declare(strict_types=1);

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

// Use Artisan::call() and Artisan::output() for these tests.
// PendingCommand matches only one expected substring per output line.
// It fails when two expected substrings appear in the same line.
// The command prints its status in one line.

beforeEach(function () {
    config()->set('laravel-imgproxy', [
        'default' => 'default',
        'instances' => [
            'default' => [
                'url' => 'https://imgproxy.example.com',
                'key' => null,
                'salt' => null,
                'signature_size' => null,
                'encoding' => 'base64',
            ],
            'staging' => [
                'url' => 'https://imgproxy.staging.example.com',
                'key' => null,
                'salt' => null,
                'signature_size' => null,
                'encoding' => 'base64',
            ],
        ],
        'presets' => [],
    ]);
});

it('checks the default instance health and exits zero on success', function () {
    Http::fake([
        'https://imgproxy.example.com/health' => Http::response('OK', 200),
    ]);

    $exitCode = Artisan::call('imgproxy:health');
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('[default]')
        ->and($output)->toContain('HTTP 200');
});

it('checks a named instance with the --instance option', function () {
    Http::fake([
        'https://imgproxy.staging.example.com/health' => Http::response('OK', 200),
    ]);

    $exitCode = Artisan::call('imgproxy:health', ['--instance' => 'staging']);
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('[staging]')
        ->and($output)->toContain('HTTP 200');
});

it('fails when the instance is not configured', function () {
    $exitCode = Artisan::call('imgproxy:health', ['--instance' => 'missing']);
    $output = Artisan::output();

    expect($exitCode)->not->toBe(0)
        ->and($output)->toContain('missing')
        ->and($output)->toContain('not configured');
});

it('fails with a clear message when the server is unhealthy', function () {
    Http::fake([
        'https://imgproxy.example.com/health' => Http::response('Service Unavailable', 503),
    ]);

    $exitCode = Artisan::call('imgproxy:health');
    $output = Artisan::output();

    expect($exitCode)->not->toBe(0)
        ->and($output)->toContain('[default]')
        ->and($output)->toContain('HTTP 503');
});

it('fails when the server cannot be reached', function () {
    Http::fake(fn ($request) => throw new ConnectionException('Connection refused'));

    $exitCode = Artisan::call('imgproxy:health');
    $output = Artisan::output();

    expect($exitCode)->not->toBe(0)
        ->and($output)->toContain('[default]')
        ->and($output)->toContain('Connection refused');
});
