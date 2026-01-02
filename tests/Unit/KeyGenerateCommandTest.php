<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Commands\KeyGenerateCommand;

describe('KeyGenerateCommand', function () {
    describe('key generation', function () {
        it('generates 64 character hex key', function () {
            $key = bin2hex(random_bytes(32));

            expect(strlen($key))->toBe(64);
            expect(preg_match('/^[a-f0-9]+$/', $key))->toBe(1);
        });

        it('generates 64 character hex salt', function () {
            $salt = bin2hex(random_bytes(32));

            expect(strlen($salt))->toBe(64);
            expect(preg_match('/^[a-f0-9]+$/', $salt))->toBe(1);
        });

        it('generates unique key and salt', function () {
            $key1 = bin2hex(random_bytes(32));
            $salt1 = bin2hex(random_bytes(32));
            $key2 = bin2hex(random_bytes(32));
            $salt2 = bin2hex(random_bytes(32));

            expect($key1)->not->toBe($key2);
            expect($salt1)->not->toBe($salt2);
            expect($key1)->not->toBe($salt1);
        });
    });

    describe('command signature', function () {
        it('has correct signature', function () {
            $command = new KeyGenerateCommand;

            expect($command->getName())->toBe('imgproxy:key');
        });
    });

    describe('command execution', function () {
        it('generates and saves keys to .env file', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, '');

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('updates existing .env keys', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, "IMGPROXY_KEY=oldkey\nIMGPROXY_SALT=oldsalt\n");

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->not->toContain('oldkey');
            expect($content)->not->toContain('oldsalt');

            unlink($envPath);
        });

        it('appends keys when not present in .env', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, "APP_NAME=test\n");

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toContain('APP_NAME=test');
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('generates 64 character hex strings', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, '');

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            preg_match('/IMGPROXY_KEY=([a-f0-9]+)/', $content, $keyMatch);
            preg_match('/IMGPROXY_SALT=([a-f0-9]+)/', $content, $saltMatch);

            expect($keyMatch[1])->toHaveLength(64);
            expect($saltMatch[1])->toHaveLength(64);

            unlink($envPath);
        });

        it('handles .env file that does not exist', function () {
            $envPath = base_path('.env');

            if (file_exists($envPath)) {
                unlink($envPath);
            }

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('updates salt when it already exists', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, "APP_NAME=test\nIMGPROXY_SALT=existing_salt\nOTHER=value\n");

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toContain('APP_NAME=test');
            expect($content)->toContain('OTHER=value');
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            // Salt is replaced with a new generated value
            expect($content)->not->toContain('IMGPROXY_SALT=existing_salt');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('preserves other environment variables', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, "APP_ENV=production\nDB_CONNECTION=pgsql\nIMGPROXY_KEY=old\n");

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toContain('APP_ENV=production');
            expect($content)->toContain('DB_CONNECTION=pgsql');
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('handles empty .env file', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, '');

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toMatch('/^IMGPROXY_KEY=[a-f0-9]{64}$/m');
            expect($content)->toMatch('/^IMGPROXY_SALT=[a-f0-9]{64}$/m');

            unlink($envPath);
        });

        it('handles .env with only comments', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, "# This is a comment\n");

            $this->artisan('imgproxy:key')->assertExitCode(0);

            $content = file_get_contents($envPath);
            expect($content)->toContain('# This is a comment');
            expect($content)->toMatch('/\nIMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/\nIMGPROXY_SALT=[a-f0-9]{64}/m');

            unlink($envPath);
        });

        it('returns success exit code', function () {
            $envPath = base_path('.env');
            file_put_contents($envPath, '');

            $this->artisan('imgproxy:key')->assertSuccessful();

            unlink($envPath);
        });
    });
});
