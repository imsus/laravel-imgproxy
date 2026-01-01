<?php

namespace Imsus\ImgProxy\Tests;

use Imsus\ImgProxy\Enums\OutputExtension;
use Imsus\ImgProxy\Enums\ResizeType;
use Imsus\ImgProxy\Facades\ImgProxy;

beforeEach(function () {
    $this->sampleImageUrl = 'https://picsum.photos/800/600';
});

it('can make HTTP request to workbench basic test endpoint', function () {
    $response = $this->get('/imgproxy-test/basic');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'processed',
            'test',
        ])
        ->assertJson([
            'test' => 'basic_url_generation',
        ]);

    $data = $response->json();
    expect($data['processed'])->toContain('width:400')
        ->and($data['processed'])->toContain('height:300');
});

it('can make HTTP request to workbench effects test endpoint', function () {
    $response = $this->get('/imgproxy-test/effects');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'processed',
            'effects_applied',
            'test',
        ])
        ->assertJson([
            'test' => 'effects_and_quality',
        ]);

    $data = $response->json();
    expect($data['processed'])->toContain('quality:85')
        ->and($data['processed'])->toContain('blur:1');
});

it('can make HTTP request to workbench formats test endpoint', function () {
    $response = $this->get('/imgproxy-test/formats');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'formats' => [
                'jpeg',
                'png',
                'webp',
                'avif',
            ],
            'test',
        ])
        ->assertJson([
            'test' => 'format_conversion',
        ]);

    $data = $response->json();
    expect($data['formats']['jpeg'])->toContain('.jpg')
        ->and($data['formats']['png'])->toContain('.png')
        ->and($data['formats']['webp'])->toContain('.webp')
        ->and($data['formats']['avif'])->toContain('.avif');
});

it('can make HTTP request to workbench resize test endpoint', function () {
    $response = $this->get('/imgproxy-test/resize');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'resize_types' => [
                'fit',
                'fill',
                'force',
                'auto',
            ],
            'test',
        ]);

    $data = $response->json();
    expect($data['resize_types']['fit'])->toContain('resizing_type:fit')
        ->and($data['resize_types']['fill'])->toContain('resizing_type:fill')
        ->and($data['resize_types']['force'])->toContain('resizing_type:force')
        ->and($data['resize_types']['auto'])->toContain('resizing_type:auto');
});

it('can make HTTP request to facade vs helper comparison endpoint', function () {
    $response = $this->get('/imgproxy-test/facade-vs-helper');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'facade_result',
            'helper_result',
            'urls_match',
            'test',
        ])
        ->assertJson([
            'urls_match' => true,
            'test' => 'facade_vs_helper',
        ]);
});

it('can make HTTP request to configuration test endpoint', function () {
    $response = $this->get('/imgproxy-test/config');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'endpoint',
            'has_key',
            'has_salt',
            'default_source_url_mode',
            'default_output_extension',
            'test',
        ])
        ->assertJson([
            'test' => 'configuration',
        ]);

    $data = $response->json();
    expect($data['endpoint'])->toBe('http://localhost:8080')
        ->and($data['default_source_url_mode'])->toBe('encoded')
        ->and($data['default_output_extension'])->toBe('jpeg');
});

it('can make HTTP request to error handling test endpoint', function () {
    $response = $this->get('/imgproxy-test/error-handling');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'invalid_url_handling',
            'invalid_quality_error',
            'test',
        ])
        ->assertJson([
            'test' => 'error_handling',
        ]);

    $data = $response->json();
    expect($data['invalid_url_handling'])->toBe('not-a-valid-url') // Should return original invalid URL
        ->and($data['invalid_quality_error'])->toContain('Quality must be between 0 and 100');
});

it('can make HTTP request to performance test endpoint', function () {
    $response = $this->get('/imgproxy-test/performance');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'urls_generated',
            'duration_seconds',
            'urls_per_second',
            'sample_urls',
            'test',
        ])
        ->assertJson([
            'urls_generated' => 100,
            'test' => 'performance',
        ]);

    $data = $response->json();
    expect($data['urls_per_second'])->toBeGreaterThan(100) // Should generate URLs quickly
        ->and($data['sample_urls'])->toHaveCount(3);
});

it('can access workbench test index endpoint', function () {
    $response = $this->get('/imgproxy-test/');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'available_tests',
            'usage',
        ]);

    $data = $response->json();
    expect($data['available_tests'])->toHaveCount(10)
        ->and($data['message'])->toContain('ImgProxy Laravel Package Test Suite');
});

// Test that the package is properly integrated in workbench environment
it('has imgproxy helper function available in workbench', function () {
    expect(function_exists('imgproxy'))->toBeTrue();
});

it('has ImgProxy facade available in workbench', function () {
    $url = ImgProxy::url($this->sampleImageUrl)
        ->setWidth(200)
        ->build();

    expect($url)->toBeString()
        ->and($url)->toContain('width:200');
});

it('can use all enum classes in workbench', function () {
    $url = imgproxy($this->sampleImageUrl)
        ->setWidth(300)
        ->setHeight(200)
        ->setResizeType(ResizeType::FILL)
        ->setExtension(OutputExtension::WEBP)
        ->build();

    expect($url)->toContain('resizing_type:fill')
        ->and($url)->toContain('.webp');
});

it('can access imgproxy config in workbench', function () {
    expect(config('imgproxy.endpoint'))->toBe('http://localhost:8080')
        ->and(config('imgproxy.default_source_url_mode'))->toBe('encoded')
        ->and(config('imgproxy.default_output_extension'))->toBe('jpeg');
});

it('can make HTTP request to workbench core processing test endpoint', function () {
    $response = $this->get('/imgproxy-test/core-processing');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'processed',
            'processing_applied',
            'test',
        ])
        ->assertJson([
            'test' => 'core_processing',
        ]);

    $data = $response->json();
    expect($data['processed'])->toContain('width:500')
        ->and($data['processed'])->toContain('height:400')
        ->and($data['processed'])->toContain('pd:20')
        ->and($data['processed'])->toContain('bg:FF5733')
        ->and($data['processed'])->toContain('ar:1')
        ->and($data['processed'])->toContain('rot:90')
        ->and($data['processed'])->toContain('sm:1')
        ->and($data['processed'])->toContain('trim:15')
        ->and($data['processed'])->toContain('pix:10');
});

it('can make HTTP request to workbench watermark test endpoint', function () {
    $response = $this->get('/imgproxy-test/watermark');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'original',
            'processed',
            'watermark_applied',
            'test',
            'note',
        ])
        ->assertJson([
            'test' => 'watermark',
        ]);

    $data = $response->json();
    expect($data['processed'])->toContain('width:500')
        ->and($data['processed'])->toContain('height:400')
        ->and($data['processed'])->toContain('wm:0.7:soea:0:0:0.25');
});
