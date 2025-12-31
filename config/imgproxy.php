<?php

return [
    'endpoint' => env('IMGPROXY_ENDPOINT', 'http://localhost:8080'),
    'key' => env('IMGPROXY_KEY'),
    'salt' => env('IMGPROXY_SALT'),
    'default_source_url_mode' => env('IMGPROXY_DEFAULT_SOURCE_URL_MODE', 'encoded'),
    'default_output_extension' => env('IMGPROXY_DEFAULT_OUTPUT_EXTENSION', 'jpeg'),
    'default_gravity' => env('IMGPROXY_DEFAULT_GRAVITY', 'ce'),
];
