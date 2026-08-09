<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

it('prints a fresh 32-byte hex key and salt pair for the config', function () {
    $exitCode = Artisan::call('imgproxy:key');

    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('IMGPROXY_KEY=')
        ->and($output)->toContain('IMGPROXY_SALT=')
        ->and($output)->toMatch('/IMGPROXY_KEY=[0-9a-f]{64}/')
        ->and($output)->toMatch('/IMGPROXY_SALT=[0-9a-f]{64}/');
});

it('generates a different pair on every run', function () {
    Artisan::call('imgproxy:key');
    $first = Artisan::output();

    Artisan::call('imgproxy:key');
    $second = Artisan::output();

    expect($first)->not->toBe($second);
});
