# Contributing to OwnPay Laravel SDK

Thank you for considering contributing to the OwnPay Laravel SDK! This document provides guidelines and instructions for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)
- [Reporting Issues](#reporting-issues)
- [Security Vulnerabilities](#security-vulnerabilities)

## Code of Conduct

This project adheres to the [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to [dev@ownpay.org](mailto:dev@ownpay.org).

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a new branch for your feature or bug fix
4. Make your changes
5. Submit a pull request

## Development Setup

### Prerequisites

- PHP 8.3 or higher
- Composer
- Required PHP extensions: `bcmath`, `hash`, `json`

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/ownpay-laravel.git
cd ownpay-laravel

# Install dependencies
composer install

# Run tests to verify setup
composer test
```

### Development Dependencies

The package uses the following development tools:

- **PHPUnit** — Testing framework
- **Laravel Pint** — Code style fixer
- **Laravel/Pest** — PHPStan for static analysis (Larastan)

## Coding Standards

### PHP Version

- Minimum PHP version: 8.3
- Use modern PHP features: enums, readonly properties, constructor promotion, named arguments

### Code Style

The project follows PSR-12 coding standards with additional rules defined in `pint.json`:

```bash
# Check code style
composer format

# Fix code style automatically
./vendor/bin/pint
```

### Key Rules

- **Always use `declare(strict_types=1)`** at the top of every PHP file
- **Use PHPDoc annotations** for complex types (array shapes, generics)
- **Prefer `readonly` classes** for value objects and DTOs
- **Use enums** for fixed value sets
- **Use `#[\SensitiveParameter]`** on all secret/token parameters
- **Use `match` expressions** instead of switch statements where appropriate

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `PaymentService` |
| Methods | camelCase | `createPayment` |
| Constants | UPPER_SNAKE_CASE | `MAX_RETRIES` |
| Enums | PascalCase | `PaymentStatus` |
| Enum cases | PascalCase | `PaymentStatus::Completed` |
| Variables | camelCase | `$paymentId` |

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run specific test file
./vendor/bin/phpunit tests/Unit/ValueObjects/MoneyTest.php

# Run tests matching a pattern
./vendor/bin/phpunit --filter "MoneyTest"
```

### Writing Tests

- Place unit tests in `tests/Unit/`
- Place feature tests in `tests/Feature/`
- Extend `Orchestra\Testbench\TestCase` for tests that need Laravel
- Extend `PHPUnit\Framework\TestCase` for pure unit tests
- Use descriptive test method names: `test_can_create_payment_with_valid_data`

### Test Naming Convention

```php
// Good
public function test_can_create_payment_with_valid_data(): void
{
    // ...
}

public function test_throws_exception_when_amount_is_negative(): void
{
    // ...
}

// Bad
public function testPayment(): void
{
    // ...
}
```

### Static Analysis

```bash
# Run PHPStan (Level 9)
composer analyse
```

All code must pass PHPStan Level 9 analysis with zero errors.

## Pull Request Process

### Before Submitting

1. **Run all checks:**
   ```bash
   composer format    # Code style
   composer analyse   # Static analysis
   composer test      # Tests
   ```

2. **Update documentation** if needed
3. **Add tests** for new features
4. **Update CHANGELOG.md** with your changes

### PR Guidelines

- **One feature per PR** — Keep pull requests focused
- **Write clear commit messages** — Use conventional commits format:
  ```
  feat: Add new feature
  fix: Fix bug description
  docs: Update documentation
  test: Add missing tests
  refactor: Refactor code
  style: Fix code style
  chore: Update dependencies
  ```

- **Reference issues** — Link related issues in the PR description
- **Keep PRs small** — Easier to review and merge

### PR Template

When creating a PR, use the provided template in `.github/PULL_REQUEST_TEMPLATE.md`.

## Reporting Issues

### Bug Reports

When reporting bugs, please include:

1. **PHP version** (`php -v`)
2. **Laravel version**
3. **Package version**
4. **Steps to reproduce**
5. **Expected behavior**
6. **Actual behavior**
7. **Error messages/logs**

### Feature Requests

When requesting features, please include:

1. **Use case** — Why do you need this feature?
2. **Proposed solution** — How should it work?
3. **Alternatives considered** — What other approaches did you consider?

## Security Vulnerabilities

**Do NOT open a public issue for security vulnerabilities.**

Please report security vulnerabilities to [security@ownpay.org](mailto:security@ownpay.org). See our [Security Policy](SECURITY.md) for details.

## Development Workflow

### Branch Naming

- `feature/description` — New features
- `fix/description` — Bug fixes
- `docs/description` — Documentation changes
- `refactor/description` — Code refactoring

### Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

Types:
- `feat` — New feature
- `fix` — Bug fix
- `docs` — Documentation
- `style` — Code style (formatting, etc.)
- `refactor` — Code refactoring
- `test` — Adding tests
- `chore` — Maintenance tasks

### Release Process

1. Update `CHANGELOG.md` with all changes
2. Update version in any relevant files
3. Create a git tag: `git tag -a v1.0.0 -m "Release v1.0.0"`
4. Push the tag: `git push origin v1.0.0`
5. Create a GitHub release

## Questions?

If you have questions about contributing, please:

1. Check the [documentation](README.md)
2. Search [existing issues](https://github.com/own-pay/ownpay-laravel/issues)
3. Open a [new discussion](https://github.com/own-pay/ownpay-laravel/discussions)

Thank you for contributing! 🎉
