<?php

namespace Imsus\ImgProxy;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ImgProxyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-imgproxy')
            ->hasConfigFile();
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(ImgProxy::class, function ($app) {
            return new ImgProxy;
        });

        $this->loadHelpers();
        $this->loadViews();
        $this->loadComponents();
    }

    protected function loadHelpers(): void
    {
        require_once __DIR__.'/helpers.php';
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'imgproxy');
    }

    protected function loadComponents(): void
    {
        $this->loadViewComponentsAs('imgproxy', [
            Components\ImgProxyComponent::class,
        ]);
    }
}
