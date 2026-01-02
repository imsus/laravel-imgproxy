<?php

use Illuminate\Filesystem\FilesystemAdapter;
use Imsus\ImgProxy\ImgProxy;

FilesystemAdapter::macro('imgproxy', function (string $path): ImgProxy {
    return ImgProxy::fromStorage($this, $path);
});
