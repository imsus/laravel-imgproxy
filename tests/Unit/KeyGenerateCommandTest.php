<?php

namespace Imsus\ImgProxy\Tests\Unit;

use Imsus\ImgProxy\Commands\KeyGenerateCommand;

describe('KeyGenerateCommand', function () {
    describe('key generation', function () {
        it('generates 64 character hex key', function () {
            $key = fake()->regexify('[a-f0-9]{64}');

            expect(strlen($key))->toBe(64);
            expect(preg_match('/^[a-f0-9]+$/', $key))->toBe(1);
        });

        it('generates 64 character hex salt', function () {
            $salt = fake()->regexify('[a-f0-9]{64}');

            expect(strlen($salt))->toBe(64);
            expect(preg_match('/^[a-f0-9]+$/', $salt))->toBe(1);
        });

        it('generates unique key and salt', function () {
            $key1 = fake()->regexify('[a-f0-9]{64}');
            $salt1 = fake()->regexify('[a-f0-9]{64}');
            $key2 = fake()->regexify('[a-f0-9]{64}');
            $salt2 = fake()->regexify('[a-f0-9]{64}');

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
});
