<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PlaygroundController;

Route::get('/', PlaygroundController::class);

// The sample image for the playground. imgproxy (running in Docker) fetches
// this from the workbench server via host.docker.internal, so the demos do
// not depend on an external image host.
Route::get('/sample/blue-marble.jpg', function () {
    return response()->file(__DIR__.'/../resources/sample/blue-marble.jpg');
});

// Status proxy for the playground's "Check all" button. The browser cannot
// read imgproxy's HTTP status directly (no CORS headers), so the workbench
// fetches it server-side. Only URLs on the configured imgproxy instance are
// accepted.
Route::get('/playground/status', function (Request $request) {
    $url = (string) $request->query('url', '');

    $scheme = parse_url($url, PHP_URL_SCHEME);
    $host = parse_url($url, PHP_URL_HOST);
    $instanceHost = parse_url((string) config('laravel-imgproxy.instances.default.url'), PHP_URL_HOST);

    if (! in_array($scheme, ['http', 'https'], true) || $host === null || $host !== $instanceHost) {
        return response()->json(['status' => null], 422);
    }

    try {
        return response()->json(['status' => Http::timeout(5)->get($url)->status()]);
    } catch (ConnectionException) {
        return response()->json(['status' => null]);
    }
});
