<?php

use Imsus\ImgProxy\Components\Picture;
use Imsus\ImgProxy\Enums\Gravity;
use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;

describe('Picture component short property aliases', function () {
    it('builds urls with w alias for width', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('width:800');
        }
    });

    it('builds urls with h alias for height', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            h: 600,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('height:600');
        }
    });

    it('builds urls with q alias for quality', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            q: 85,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('quality:85');
        }
    });

    it('builds urls with fit alias for resizeType', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
            fit: ResizeType::FILL,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('resizing_type:fill');
        }
    });

    it('builds urls with grav alias for gravity', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
            fit: ResizeType::FILL,
            grav: Gravity::CENTER,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('gravity:ce');
        }
    });

    it('w alias produces same url as width', function () {
        $componentWithWidth = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 800,
            height: 600,
        );

        $componentWithW = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
        );

        $urlsWithWidth = $componentWithWidth->buildUrls();
        $urlsWithW = $componentWithW->buildUrls();

        expect($urlsWithWidth['webp'])->toBe($urlsWithW['webp']);
        expect($urlsWithWidth['avif'])->toBe($urlsWithW['avif']);
        expect($urlsWithWidth['jpg'])->toBe($urlsWithW['jpg']);
    });

    it('h alias produces same url as height', function () {
        $componentWithHeight = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            height: 600,
        );

        $componentWithH = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            h: 600,
        );

        $urlsWithHeight = $componentWithHeight->buildUrls();
        $urlsWithH = $componentWithH->buildUrls();

        expect($urlsWithHeight['webp'])->toBe($urlsWithH['webp']);
    });

    it('q alias produces same url as quality', function () {
        $componentWithQuality = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            quality: 85,
        );

        $componentWithQ = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            q: 85,
        );

        $urlsWithQuality = $componentWithQuality->buildUrls();
        $urlsWithQ = $componentWithQ->buildUrls();

        expect($urlsWithQuality['webp'])->toBe($urlsWithQ['webp']);
    });

    it('fit alias produces same url as resizeType', function () {
        $componentWithResizeType = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            resizeType: ResizeType::FILL,
        );

        $componentWithFit = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            fit: ResizeType::FILL,
        );

        $urlsWithResizeType = $componentWithResizeType->buildUrls();
        $urlsWithFit = $componentWithFit->buildUrls();

        expect($urlsWithResizeType['webp'])->toBe($urlsWithFit['webp']);
    });

    it('grav alias produces same url as gravity', function () {
        $componentWithGravity = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            gravity: Gravity::CENTER,
        );

        $componentWithGrav = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            grav: Gravity::CENTER,
        );

        $urlsWithGravity = $componentWithGravity->buildUrls();
        $urlsWithGrav = $componentWithGrav->buildUrls();

        expect($urlsWithGravity['webp'])->toBe($urlsWithGrav['webp']);
    });

    it('original property takes precedence over alias', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 800,
            height: 600,
            w: 400,
            h: 300,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('width:800')
                ->and($url)->toContain('height:600');
        }
    });

    it('works with all short aliases combined', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
            fit: ResizeType::FILL,
            q: 85,
            grav: Gravity::CENTER,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('width:800')
                ->and($url)->toContain('height:600')
                ->and($url)->toContain('resizing_type:fill')
                ->and($url)->toContain('quality:85')
                ->and($url)->toContain('gravity:ce');
        }
    });

    it('works with dpr (no alias)', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
            dpr: 2,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('width:800')
                ->and($url)->toContain('height:600')
                ->and($url)->toContain('dpr:2');
        }
    });

    it('uses default quality when not specified', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('quality:75');
        }
    });

    it('fallback url uses aliases correctly', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            w: 800,
            h: 600,
            formats: [OutputExtension::WEBP, OutputExtension::AVIF, OutputExtension::JPEG],
        );

        $fallback = $component->fallbackUrl();

        expect($fallback)->toEndWith('.jpg')
            ->and($fallback)->toContain('width:800')
            ->and($fallback)->toContain('height:600');
    });
});

describe('Picture component property precedence', function () {
    it('quality property takes precedence over q alias', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            quality: 90,
            q: 80,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('quality:90');
        }
    });

    it('resizeType property takes precedence over fit alias', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            resizeType: ResizeType::FIT,
            fit: ResizeType::FILL,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('resizing_type:fit');
        }
    });

    it('gravity property takes precedence over grav alias', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            gravity: Gravity::NORTH,
            grav: Gravity::CENTER,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('gravity:no');
        }
    });

    it('all original properties take precedence over all aliases', function () {
        $component = new Picture(
            src: 'https://example.com/image.jpg',
            alt: 'Test image',
            width: 1000,
            height: 800,
            quality: 95,
            resizeType: ResizeType::FIT,
            gravity: Gravity::SOUTH_EAST,
            w: 800,
            h: 600,
            q: 80,
            fit: ResizeType::FILL,
            grav: Gravity::CENTER,
        );

        $urls = $component->buildUrls();

        foreach ($urls as $url) {
            expect($url)->toContain('width:1000')
                ->and($url)->toContain('height:800')
                ->and($url)->toContain('quality:95')
                ->and($url)->toContain('resizing_type:fit')
                ->and($url)->toContain('gravity:soea');
        }
    });
});
