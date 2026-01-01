<?php

use Imsus\ImgProxy\ImgProxy;

if (! function_exists('imgproxy')) {
    function imgproxy(string $url): ImgProxy
    {
        return (new ImgProxy)->url($url);
    }
}
