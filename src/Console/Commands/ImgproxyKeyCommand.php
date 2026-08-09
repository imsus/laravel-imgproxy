<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\Console\Commands;

use Illuminate\Console\Command;

/**
 * Generates a fresh key and salt pair for imgproxy URL signing.
 *
 * Prints the pair as IMGPROXY_KEY and IMGPROXY_SALT environment lines so the
 * values can be copied straight into the application's .env file.
 */
final class ImgproxyKeyCommand extends Command
{
    protected $signature = 'imgproxy:key';

    protected $description = 'Generate a 32-byte hex key and salt pair for imgproxy URL signing';

    public function handle(): int
    {
        $key = bin2hex(random_bytes(32));
        $salt = bin2hex(random_bytes(32));

        $this->info('Generated a new imgproxy key and salt pair.');
        $this->newLine();
        $this->line("IMGPROXY_KEY={$key}");
        $this->line("IMGPROXY_SALT={$salt}");
        $this->newLine();
        $this->comment('Add these lines to your .env file to enable signed URLs.');

        return self::SUCCESS;
    }
}
