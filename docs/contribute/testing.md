---
title: Testing
description: The v2 test stack, how to run tests, and how to run live integration checks against a real imgproxy.
---

# Testing

## Test Stack

| Tool | Version | Purpose |
| --- | --- | --- |
| [Pest](https://pestphp.com/) | 5 | Test framework |
| [pest-plugin-type-coverage](https://pestphp.com/docs/plugins/type-coverage) | 5 | Type coverage enforcement |
| [PHPStan](https://phpstan.org/) + [Larastan](https://github.com/nunomaduro/larastan) | 3.x / 3.9+ | Static analysis |
| [Pint](https://github.com/laravel/pint) | 1.29+ | Code style |
| [Orchestra Testbench](https://github.com/orchestral/testbench) | 11 | Laravel package testing |

The test suite has **186 tests** and **347 assertions**.

## Running Tests

```bash
# Full validation (analyse + lint:check + test:types + test:unit)
composer test

# Static analysis only
composer analyse

# Code style check only
composer lint:check

# Type coverage only (100% enforced)
composer test:types

# Unit tests only
composer test:unit
```

## Type Coverage

Type coverage is enforced at **100%** via the `test:types` script:

```bash
composer test:types --min=100
```

Every parameter, return type, and property type must be explicitly declared. New code that lowers coverage will fail CI.

## Golden-Vector Signing Tests

The test suite includes golden-vector tests for URL signing (`src/UrlSigner.php`). These verify that the HMAC-SHA256 signature, URL-safe base64 encoding, and `signature_size` truncation produce byte-exact results against known inputs. They run without any external dependencies.

## Live Integration Tests

The test suite includes live checks against a real imgproxy running locally in Docker. They are gated behind three environment variables and **skip when they are not set** (CI never sets them):

```dotenv
IMGPROXY_URL=http://localhost:8081
IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000
IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000
```

The key and salt must match the local server's `IMGPROXY_KEY` / `IMGPROXY_SALT`.

### Running Live Tests

1. Start the local imgproxy server:

```bash
docker run -d -p 8081:8080 \
    -e IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000 \
    -e IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000 \
    imgproxy/imgproxy
```

2. Export the environment variables and run the test suite:

```bash
export IMGPROXY_URL=http://localhost:8081
export IMGPROXY_KEY=0000000000000000000000000000000000000000000000000000000000000000
export IMGPROXY_SALT=0000000000000000000000000000000000000000000000000000000000000000

composer test
```

With the variables exported, the live tests run as part of the normal `composer test` invocation.

## Workbench Playground

The `workbench/` directory contains a live review app for interactive testing. See [Contributing](/contribute/contributing#workbench-playground) for setup instructions.
