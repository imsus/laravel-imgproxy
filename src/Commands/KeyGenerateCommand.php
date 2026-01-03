<?php

namespace Imsus\ImgProxy\Commands;

use Illuminate\Console\Command;

class KeyGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imgproxy:key {--p|path= : Path to .env file (defaults to .env in project root)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate IMGPROXY_KEY and IMGPROXY_SALT';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = bin2hex(random_bytes(32));
        $salt = bin2hex(random_bytes(32));

        $this->info('IMGPROXY_KEY='.$key);
        $this->info('IMGPROXY_SALT='.$salt);

        $envPath = $this->option('path') ?: base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        $envContent = $this->updateEnvValue($envContent, 'IMGPROXY_KEY', $key);
        $envContent = $this->updateEnvValue($envContent, 'IMGPROXY_SALT', $salt);

        try {
            $result = file_put_contents($envPath, $envContent);
        } catch (\Throwable $e) {
            $result = false;
        }

        if ($result === false) {
            $this->error('Failed to save keys to the .env file.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->task('Keys generated and saved to .env');

        return self::SUCCESS;
    }

    /**
     * Update or append an environment variable in the content string.
     */
    private function updateEnvValue(string $content, string $key, string $value): string
    {
        $pattern = '/^'.$key.'=.*$/m';
        $replacement = $key.'='.$value;
        $content = preg_replace($pattern, $replacement, $content, -1, $count);

        if ($count === 0) {
            $content .= "\n".$key.'='.$value;
        }

        return $content;
    }
}
