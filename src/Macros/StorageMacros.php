<?php

use Illuminate\Support\Facades\Storage;
use Imsus\ImgProxy\ImgProxy;

Storage::macro('imgproxy', function (string $path): ImgProxy {
    $disk = $this;

    // Check if the disk supports temporaryUrl (private disks like S3)
    if (method_exists($disk, 'temporaryUrl')) {
        try {
            // Attempt to get a temporary URL, which indicates a private disk
            // We'll use the default expiration (1 hour) if not specified
            $url = $disk->temporaryUrl($path);

            return imgproxy($url);
        } catch (\Throwable $e) {
            // If temporaryUrl fails (e.g., driver doesn't support it or misconfigured),
            // fall back to using the regular URL method
        }
    }

    // For public disks, use the regular url() method
    return imgproxy($disk->url($path));
});
