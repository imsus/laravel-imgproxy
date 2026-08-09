<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy\Tests;

use Imsus\LaravelImgproxy\LaravelImgproxyServiceProvider;
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
