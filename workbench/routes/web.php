<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PlaygroundController;

Route::get('/', PlaygroundController::class);

// The sample image for the playground. imgproxy (running in Docker) fetches
// this from the workbench server via host.docker.internal, so the demos do
// not depend on an external image host.
Route::get('/sample/blue-marble.jpg', function () {
    return response()->file(__DIR__.'/../resources/sample/blue-marble.jpg');
});
