<?php

namespace Imsus\ImgProxy;

use Imsus\ImgProxy\Commands\KeyGenerateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ImgProxyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-imgproxy')
            ->hasConfigFile()
            ->hasViewComponents('imgproxy', Components\Img::class)
            ->hasViewComponents('imgproxy', Components\Picture::class)
            ->hasCommands(KeyGenerateCommand::class);
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(ImgProxy::class, fn () => new ImgProxy);
        $this->registerHelpers();
    }

    protected function registerHelpers(): void
    {
        require_once __DIR__.'/helpers.php';
    }
}
