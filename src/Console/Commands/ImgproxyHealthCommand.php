<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Imsus\LaravelImgproxy\Manager;
use InvalidArgumentException;

/**
 * Checks the health of an imgproxy instance by requesting its /health
 * endpoint. Exits non-zero when the instance is unreachable, unhealthy, or
 * not configured.
 */
final class ImgproxyHealthCommand extends Command
{
    /**
     * The HTTP timeout, in seconds, for the health request.
     */
    private const int TIMEOUT_SECONDS = 5;

    protected $signature = 'imgproxy:health {--instance= : The imgproxy instance to check}';

    protected $description = 'Check the health of an imgproxy instance';

    public function handle(Manager $manager): int
    {
        $name = $this->option('instance');

        if (! is_string($name) || $name === '') {
            $name = $manager->defaultInstance();
        }

        try {
            $instance = $manager->instance($name);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)->get(rtrim($instance->baseUrl(), '/').'/health');
        } catch (ConnectionException $exception) {
            $this->error("The imgproxy instance [{$name}] is unreachable: {$exception->getMessage()}.");

            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->error("The imgproxy instance [{$name}] is unhealthy: HTTP {$response->status()}.");

            return self::FAILURE;
        }

        $this->info("The imgproxy instance [{$name}] is healthy: HTTP {$response->status()}.");

        return self::SUCCESS;
    }
}
