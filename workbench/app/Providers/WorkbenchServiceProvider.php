<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // The serve app's base path is the package root, so workbench/config
        // is not picked up by the normal config loader. Merge the demo config
        // (presets) here, but only outside the test suite — tests assert the
        // package defaults.
        if ($this->app->runningUnitTests()) {
            return;
        }

        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-imgproxy.php', 'laravel-imgproxy');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
