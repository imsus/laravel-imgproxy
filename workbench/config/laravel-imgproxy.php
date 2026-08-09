<?php

declare(strict_types=1);

return [

    'default' => 'default',

    'instances' => [
        'default' => [
            'url' => env('IMGPROXY_URL'),
            'key' => env('IMGPROXY_KEY'),
            'salt' => env('IMGPROXY_SALT'),
            'signature_size' => null,
            'encoding' => env('IMGPROXY_ENCODING', 'base64'),
        ],
    ],

    'presets' => [
        'thumb' => [
            'resize' => 'fill',
            'width' => 300,
            'height' => 300,
        ],
        'hero' => [
            'resize' => 'fill',
            'width' => 1200,
            'height' => 600,
            'quality' => 85,
        ],
    ],

    'source' => env('PLAYGROUND_SOURCE', 'http://host.docker.internal:8000/sample/blue-marble.jpg'),

];
