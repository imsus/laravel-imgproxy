# Contributing Guide

Thank you for considering contributing to Laravel-ImgProxy! This document outlines the process for contributing to this project.

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 11+ (for development workbench)
- Docker (optional, for running ImgProxy server)

### Development Setup

1. **Fork the repository**

   Visit [laravel-imgproxy](https://github.com/imsus/laravel-imgproxy) and click the "Fork" button.

2. **Clone your fork**

   ```bash
   git clone https://github.com/YOUR-USERNAME/laravel-imgproxy.git
   cd laravel-imgproxy
   ```

3. **Install dependencies**

   ```bash
   composer install
   ```

4. **Set up the workbench environment**

   ```bash
   composer build
   ```

5. **Start the development server**

   ```bash
   composer start
   ```

6. **Run tests**

   ```bash
   composer test
   ```

## Coding Standards

This project follows the [Laravel coding standards](https://laravel.com/docs/12.x/pint) enforced by [Pint](https://pint.laravel.com/).

### Formatting

Before committing, format your code:

```bash
composer format
```

### Static Analysis

Run PHPStan to check for type errors:

```bash
composer analyse
```

## Testing

All contributions should be tested. This project uses [Pest](https://pest.laravel.com/) for testing.

```bash
composer test
```

Write tests for:
- New features
- Bug fixes
- Edge cases
- API behavior

## Pull Request Process

1. **Create a branch**

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**

   Follow the coding standards and write tests.

3. **Commit your changes**

   ```bash
   git commit -m "feat: add your feature description"
   ```

   We follow [Conventional Commits](https://www.conventionalcommits.org/):
   - `feat:` for new features
   - `fix:` for bug fixes
   - `refactor:` for code refactoring
   - `docs:` for documentation changes
   - `test:` for test additions

4. **Push to GitHub**

   ```bash
   git push origin feature/your-feature-name
   ```

5. **Open a Pull Request**

   Visit the repository and click "New Pull Request".

   Fill in the PR template with:
   - A clear title
   - Description of changes
   - Any related issues
   - Test results (if applicable)

6. **Review process**

   - Maintainers will review your PR
   - Address any requested changes
   - Once approved, your PR will be merged

## Documentation

- Update the `README.md` for user-facing changes
- Add inline PHP docblocks for new public methods
- Update `CHANGELOG.md` with your changes under the appropriate version

## Code of Conduct

Please note that this project is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## Questions?

If you have questions, feel free to:
- Open a [GitHub Discussion](https://github.com/imsus/laravel-imgproxy/discussions)
- Ask in the PR comments
