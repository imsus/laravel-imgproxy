<?php

use Imsus\ImgProxy\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Enable short options for the test suite.
 */
function withShortOptions(): void
{
    config()->set('imgproxy.use_short_options', true);
}

/**
 * Disable short options for the test suite.
 */
function withoutShortOptions(): void
{
    config()->set('imgproxy.use_short_options', false);
}
