# Contributing

Thank you for your interest in contributing to Laravel ImgProxy! This document provides guidelines and instructions for contributing.

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Laravel 10.x or 11.x (for testing)

### Development Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/laravel-imgproxy.git
   ```
3. Install dependencies:
   ```bash
   composer install
   ```

## Testing

Before submitting a pull request, ensure all tests pass:

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage
```

See [Testing Guide](/contribute/testing) for more details.

## Code Style

This project uses [Pint](https://github.com/laravel/pint) for code formatting:

```bash
# Format code
composer format
```

## Submitting Changes

1. Create a feature branch from `main`
2. Make your changes
3. Ensure tests pass
4. Submit a pull request

## Reporting Issues

When reporting issues, please include:

- PHP version
- Laravel version
- Package version
- Steps to reproduce
- Expected vs actual behavior
