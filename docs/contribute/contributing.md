---
title: Contributing
description: Guidelines for contributing to Laravel imgproxy v2.
---

# Contributing

Thank you for considering contributing to Laravel imgproxy! For significant changes, please [open an issue](https://github.com/imsus/laravel-imgproxy/issues) first so the approach can be discussed.

## Prerequisites

- PHP 8.4+
- Composer
- Laravel 13 (for testing via Testbench)
- An imgproxy server (optional, for live integration tests — see [Testing](/contribute/testing))

## Development Setup

Fork and clone the repository, then install dependencies:

```bash
git clone https://github.com/YOUR_USERNAME/laravel-imgproxy.git
cd laravel-imgproxy
composer install
```

### Package Validation Commands

Run these before submitting a PR:

| Command | What it does |
| --- | --- |
| `composer test` | Full validation: `analyse` + `lint:check` + `test:types` + `test:unit` |
| `composer lint:check` | Check code style with [Pint](https://github.com/laravel/pint) (dry-run) |
| `composer lint` | Auto-fix code style with Pint |
| `composer analyse` | Static analysis with [PHPStan](https://phpstan.org/) via [Larastan](https://github.com/nunomaduro/larastan) |
| `composer test:types` | Run Pest with [type-coverage](https://pestphp.com/docs/plugins/type-coverage) — 100% enforced |
| `composer test:unit` | Run the Pest test suite |

### Workbench Playground

The `workbench/` directory contains a live review app that renders real URLs and images through the package against a local imgproxy:

1. Point `workbench/.env` at an imgproxy server. Copy `workbench/.env.example` if needed — the defaults match the local Docker imgproxy used by the live integration tests.

2. Build and serve the workbench app on all interfaces with multiple PHP workers (the Docker imgproxy must reach the host, and the "Check all" status proxy blocks a worker while imgproxy fetches the sample — one worker deadlocks):

```bash
composer build
PHP_CLI_SERVER_WORKERS=4 vendor/bin/testbench serve --host=0.0.0.0
```

3. Docker Desktop resolves `host.docker.internal` to the host. Other Docker setups (e.g. Dory) need `PLAYGROUND_SOURCE` in `workbench/.env` set to the host's LAN IP (`ipconfig getifaddr en0` on macOS).

4. Open `http://127.0.0.1:8000/` and walk the sections: source, URL builder demos, presets, Blade components, storage, commands, and validation.

The playground lives in `workbench/` and is not shipped to package consumers.

## PR Conventions

### Tests

All changes should include tests that defend the observable contract. The test suite uses [Pest](https://pestphp.com/) with [Orchestra Testbench](https://github.com/orchestral/testbench). Run `composer test` before pushing.

### Types

The project enforces 100% type coverage. New code must be fully typed; run `composer test:types` to verify.

### Style

Code style is enforced by Pint. Run `composer lint:check` to check or `composer lint` to auto-fix.

### Commit History

Send a coherent commit history. Each commit in your pull request should be meaningful. You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts. The project follows [SemVer](http://semver.org/).

## Reporting Issues

When reporting issues, please include (from the [bug report template](https://github.com/imsus/laravel-imgproxy/issues/new?template=bug.yml)):

- Package version
- Laravel version
- PHP version
- Operating system (if relevant)
- Description of the issue
- Steps to reproduce
- Notes or additional context

## Security Vulnerabilities

Please review [the security policy](https://github.com/imsus/laravel-imgproxy/blob/main/.github/SECURITY.md) on how to report security vulnerabilities.
