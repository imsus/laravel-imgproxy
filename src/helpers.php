<?php

declare(strict_types=1);

use LaravelImgproxy\LaravelImgproxy\Manager;

if (! function_exists('imgproxy')) {
    /**
     * The imgproxy manager, a terse alternative to the facade.
     */
    function imgproxy(): Manager
    {
        return app(Manager::class);
    }
}
