<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy;

use DateTimeInterface;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LaravelImgproxy\LaravelImgproxy\Console\Commands\ImgproxyHealthCommand;
use LaravelImgproxy\LaravelImgproxy\Console\Commands\ImgproxyKeyCommand;
use LaravelImgproxy\LaravelImgproxy\View\Components\Img;
use LaravelImgproxy\LaravelImgproxy\View\Components\Picture;

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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'imgproxy');

        Blade::component('imgproxy-img', Img::class);
        Blade::component('imgproxy-picture', Picture::class);

        FilesystemAdapter::macro('imgproxy', function (string $path, int|DateTimeInterface|null $expiration = null): Builder {
            /** @var FilesystemAdapter $disk */
            $disk = $this;

            return imgproxy()->url(DiskUrl::resolve($disk, $path, $expiration));
        });

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ImgproxyKeyCommand::class,
            ImgproxyHealthCommand::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/laravel-imgproxy.php' => config_path('laravel-imgproxy.php'),
        ], ['laravel-imgproxy', 'laravel-imgproxy-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/imgproxy'),
        ], ['laravel-imgproxy', 'laravel-imgproxy-views']);
    }
}
