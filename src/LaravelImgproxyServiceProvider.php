<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use Illuminate\Support\ServiceProvider;

class LaravelImgproxyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-imgproxy.php', 'laravel-imgproxy');

        $this->app->singleton(Manager::class, fn (): Manager => new Manager(config('laravel-imgproxy')));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-imgproxy.php' => config_path('laravel-imgproxy.php'),
        ], ['laravel-imgproxy', 'laravel-imgproxy-config']);
    }
}
