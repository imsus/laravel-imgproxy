<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Tests;

use LaravelImgproxy\LaravelImgproxy\LaravelImgproxyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelImgproxyServiceProvider::class,
        ];
    }
}
