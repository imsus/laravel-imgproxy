<?php

use Imsus\ImgProxy\Components\Img;
use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

describe('Img component short property aliases', function () {
    it('builds url with w alias for width', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('width:300');
    });

    it('builds url with h alias for height', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            h: 200,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('height:200');
    });

    it('builds url with q alias for quality', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            q: 85,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('quality:85');
    });

    it('builds url with fit alias for resizeType', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
            h: 200,
            fit: ResizeType::FILL,
        );

        expect($component->buildUrl())->toContain('resizing_type:fill');
    });

    it('builds url with fmt alias for format', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            fmt: OutputExtension::WEBP,
        );

        expect($component->buildUrl())->toEndWith('.webp');
    });

    it('builds url with grav alias for gravity', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
            h: 200,
            fit: ResizeType::FILL,
            grav: Gravity::CENTER,
        );

        expect($component->buildUrl())->toContain('gravity:ce');
    });

    it('w alias produces same url as width', function () {
        $componentWithWidth = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 300,
            height: 200,
        );

        $componentWithW = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
            h: 200,
        );

        expect($componentWithWidth->buildUrl())->toBe($componentWithW->buildUrl());
    });

    it('h alias produces same url as height', function () {
        $componentWithHeight = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            height: 200,
        );

        $componentWithH = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            h: 200,
        );

        expect($componentWithHeight->buildUrl())->toBe($componentWithH->buildUrl());
    });

    it('q alias produces same url as quality', function () {
        $componentWithQuality = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            quality: 85,
        );

        $componentWithQ = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            q: 85,
        );

        expect($componentWithQuality->buildUrl())->toBe($componentWithQ->buildUrl());
    });

    it('fit alias produces same url as resizeType', function () {
        $componentWithResizeType = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            resizeType: ResizeType::FILL,
        );

        $componentWithFit = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            fit: ResizeType::FILL,
        );

        expect($componentWithResizeType->buildUrl())->toBe($componentWithFit->buildUrl());
    });

    it('fmt alias produces same url as format', function () {
        $componentWithFormat = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            format: OutputExtension::WEBP,
        );

        $componentWithFmt = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            fmt: OutputExtension::WEBP,
        );

        expect($componentWithFormat->buildUrl())->toBe($componentWithFmt->buildUrl());
    });

    it('grav alias produces same url as gravity', function () {
        $componentWithGravity = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            gravity: Gravity::CENTER,
        );

        $componentWithGrav = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            grav: Gravity::CENTER,
        );

        expect($componentWithGravity->buildUrl())->toBe($componentWithGrav->buildUrl());
    });

    it('original property takes precedence over alias', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 400,
            height: 300,
            w: 300,
            h: 200,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('width:400')
            ->and($url)->toContain('height:300');
    });

    it('works with all short aliases combined', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
            h: 200,
            fit: ResizeType::FILL,
            fmt: OutputExtension::WEBP,
            q: 85,
            grav: Gravity::CENTER,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('width:300')
            ->and($url)->toContain('height:200')
            ->and($url)->toContain('resizing_type:fill')
            ->and($url)->toEndWith('.webp')
            ->and($url)->toContain('quality:85')
            ->and($url)->toContain('gravity:ce');
    });

    it('works with dpr (no alias)', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
            h: 200,
            dpr: 2,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('width:300')
            ->and($url)->toContain('height:200')
            ->and($url)->toContain('dpr:2');
    });

    it('uses default quality when not specified', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 300,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('quality:75');
    });
});

describe('Img component property precedence', function () {
    it('quality property takes precedence over q alias', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            quality: 90,
            q: 80,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('quality:90');
    });

    it('resizeType property takes precedence over fit alias', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            resizeType: ResizeType::FIT,
            fit: ResizeType::FILL,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('resizing_type:fit');
    });

    it('format property takes precedence over fmt alias', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            format: OutputExtension::AVIF,
            fmt: OutputExtension::WEBP,
        );

        $url = $component->buildUrl();

        expect($url)->toEndWith('.avif');
    });

    it('gravity property takes precedence over grav alias', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            gravity: Gravity::NORTH,
            grav: Gravity::CENTER,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('gravity:no');
    });

    it('all original properties take precedence over all aliases', function () {
        $component = new Img(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 500,
            height: 400,
            quality: 95,
            resizeType: ResizeType::FIT,
            format: OutputExtension::PNG,
            gravity: Gravity::SOUTH_EAST,
            w: 300,
            h: 200,
            q: 80,
            fit: ResizeType::FILL,
            fmt: OutputExtension::WEBP,
            grav: Gravity::CENTER,
        );

        $url = $component->buildUrl();

        expect($url)->toContain('width:500')
            ->and($url)->toContain('height:400')
            ->and($url)->toContain('quality:95')
            ->and($url)->toContain('resizing_type:fit')
            ->and($url)->toEndWith('.png')
            ->and($url)->toContain('gravity:soea');
    });
});
