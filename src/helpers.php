<?php

use Imsus\ImgProxy\ImgProxy;

if (! function_exists('imgproxy')) {
    function imgproxy(?string $url = null): ImgProxy
    {
        $instance = new ImgProxy;

        if ($url !== null) {
            return $instance->url($url);
        }

        return $instance;
    }
}
