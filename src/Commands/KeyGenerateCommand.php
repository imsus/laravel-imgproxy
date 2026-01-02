<?php

namespace Imsus\ImgProxy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class KeyGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imgproxy:key';

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
        $key = Str::random(64, 'hex');
        $salt = Str::random(64, 'hex');

        $this->info('IMGPROXY_KEY='.$key);
        $this->info('IMGPROXY_SALT='.$salt);

        $envPath = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        $pattern = '/^IMGPROXY_KEY=.*$/m';
        $replacement = 'IMGPROXY_KEY='.$key;
        $envContent = preg_replace($pattern, $replacement, $envContent, -1, $count);

        if ($count === 0) {
            $envContent .= "\nIMGPROXY_KEY=".$key;
        }

        $pattern = '/^IMGPROXY_SALT=.*$/m';
        $replacement = 'IMGPROXY_SALT='.$salt;
        $envContent = preg_replace($pattern, $replacement, $envContent, -1, $count);

        if ($count === 0) {
            $envContent .= "\nIMGPROXY_SALT=".$salt;
        }

        file_put_contents($envPath, $envContent);

        $this->newLine();
        $this->components->task('Keys generated and saved to .env');

        return self::SUCCESS;
    }
}
