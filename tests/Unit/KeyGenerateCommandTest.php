<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Illuminate\Support\Facades\File;
use Imsus\ImgProxy\Commands\KeyGenerateCommand;

describe('KeyGenerateCommand', function () {
    describe('key generation helpers', function () {
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
        it('has correct signature with --path option', function () {
            $command = new KeyGenerateCommand;

            expect($command->getName())->toBe('imgproxy:key');
            expect($command->getDefinition()->hasOption('path'))->toBeTrue();
        });
    });

    describe('command execution', function () {
        beforeEach(function () {
            $this->envPath = __DIR__.'/../../.env.fake';
            if (File::exists($this->envPath)) {
                File::delete($this->envPath);
            }
        });

        afterEach(function () {
            if (File::exists($this->envPath)) {
                File::delete($this->envPath);
            }
        });

        it('generates and saves keys to specified .env file', function () {
            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->expectsOutputToContain('IMGPROXY_KEY=')
                ->expectsOutputToContain('IMGPROXY_SALT=')
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('updates existing .env keys', function () {
            File::put($this->envPath, "IMGPROXY_KEY=oldkey\nIMGPROXY_SALT=oldsalt\n");

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->not->toContain('oldkey');
            expect($content)->not->toContain('oldsalt');
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('appends keys when not present in .env', function () {
            File::put($this->envPath, "APP_NAME=test\n");

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toContain('APP_NAME=test');
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('generates 64 character hex strings', function () {
            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            preg_match('/IMGPROXY_KEY=([a-f0-9]+)/', $content, $keyMatch);
            preg_match('/IMGPROXY_SALT=([a-f0-9]+)/', $content, $saltMatch);

            expect($keyMatch[1])->toHaveLength(64);
            expect($saltMatch[1])->toHaveLength(64);
        });

        it('creates .env file when it does not exist', function () {
            File::delete($this->envPath);

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            expect(file_exists($this->envPath))->toBeTrue();

            $content = File::get($this->envPath);
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('updates salt when it already exists', function () {
            File::put($this->envPath, "APP_NAME=test\nIMGPROXY_SALT=existing_salt\nOTHER=value\n");

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toContain('APP_NAME=test');
            expect($content)->toContain('OTHER=value');
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->not->toContain('IMGPROXY_SALT=existing_salt');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('preserves other environment variables', function () {
            File::put($this->envPath, "APP_ENV=production\nDB_CONNECTION=pgsql\nIMGPROXY_KEY=old\n");

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toContain('APP_ENV=production');
            expect($content)->toContain('DB_CONNECTION=pgsql');
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('handles empty .env file', function () {
            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/');
        });

        it('handles .env with only comments', function () {
            File::put($this->envPath, "# This is a comment\n");

            $this->artisan('imgproxy:key', ['--path' => $this->envPath])
                ->assertExitCode(0);

            $content = File::get($this->envPath);
            expect($content)->toContain('# This is a comment');
            expect($content)->toMatch('/IMGPROXY_KEY=[a-f0-9]{64}/m');
            expect($content)->toMatch('/IMGPROXY_SALT=[a-f0-9]{64}/m');
        });

        it('returns failure when writing to non-writable path', function () {
            $this->artisan('imgproxy:key', ['--path' => '/this-path-does-not-exist/.env'])
                ->expectsOutput('Failed to save keys to the .env file.')
                ->assertExitCode(1);
        });
    });
});
