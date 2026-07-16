# Security Policy

## Supported Versions

| Version | Supported          |
|---------|--------------------|
| 1.x     | ✅ Active support  |
| < 1.0   | ❌ No longer supported |

## Reporting a Vulnerability

**Please do NOT report security vulnerabilities through public GitHub issues.**

If you discover a security vulnerability in the OwnPay Laravel SDK, please report it responsibly by emailing [security@ownpay.org](mailto:security@ownpay.org).

### What to Include

When reporting a vulnerability, please include:

1. **Description** — A clear description of the vulnerability
2. **Steps to Reproduce** — Detailed steps to reproduce the issue
3. **Impact Assessment** — Potential impact of the vulnerability
4. **Affected Version(s)** — Which version(s) are affected
5. **Suggested Fix** — If you have a suggestion for fixing the issue

### Response Timeline

- **Acknowledgment** — We will acknowledge receipt within 48 hours
- **Initial Assessment** — We will provide an initial assessment within 5 business days
- **Resolution** — We aim to resolve critical vulnerabilities within 14 days

### Disclosure Policy

- We request that you give us reasonable time to address the vulnerability before public disclosure
- We will credit reporters in the security advisory (unless anonymity is requested)
- We will notify affected users once a fix is released

## Security Best Practices

### For Users of This SDK

1. **Keep the SDK updated** — Always use the latest version
2. **Protect your API keys** — Never commit API keys to version control
3. **Use environment variables** — Store sensitive values in `.env` files
4. **Verify webhook signatures** — Always verify incoming webhook signatures
5. **Use HTTPS** — Always use HTTPS for API communication
6. **Rotate keys regularly** — Rotate API keys periodically

### For Contributors

1. **Never commit secrets** — Never commit API keys, passwords, or tokens
2. **Use `#[\SensitiveParameter]`** — Mark sensitive parameters to prevent stack trace leaks
3. **Validate all input** — Always validate and sanitize user input
4. **Use parameterized queries** — Never concatenate raw SQL
5. **Follow OWASP guidelines** — Adhere to OWASP security best practices

## Security Features

The OwnPay Laravel SDK includes the following security features:

### Webhook Verification

All incoming webhooks are verified using HMAC-SHA256 signatures:

```php
use OwnPay\Laravel\Webhook\WebhookVerifier;

$verifier = new WebhookVerifier($webhookSecret);
$payload = $verifier->verify($rawPayload, $signatureHeader);
```

- Timing-safe comparison using `hash_equals()`
- Timestamp validation to prevent replay attacks
- Support for multiple signature header formats

### API Key Security

- API keys are stored with SHA-256 hashing
- Timing-safe comparison for key verification
- Automatic key expiration support
- Scope-based access control (read, write, admin)

### Input Validation

- All input is validated before processing
- Output is properly escaped to prevent XSS
- SQL injection prevention through parameterized queries

### Rate Limiting

- Built-in rate limit handling
- Automatic retry with exponential backoff
- Respects server-side rate limit headers

## Dependencies

This package has minimal dependencies to reduce attack surface:

- **Required:** `illuminate/support`, `illuminate/http`, `illuminate/config`, `illuminate/contracts`, `psr/log`
- **Required Extensions:** `ext-bcmath`, `ext-hash`, `ext-json`
- **No third-party dependencies** beyond Laravel framework

## Security Audits

This package undergoes regular security reviews:

- Static analysis with PHPStan Level 9
- Code review for security vulnerabilities
- Dependency vulnerability scanning

## Contact

For security-related inquiries:

- **Email:** [security@ownpay.org](mailto:security@ownpay.org)
- **PGP Key:** Available upon request

## Acknowledgments

We thank all security researchers who responsibly disclose vulnerabilities. Your efforts help keep the OwnPay ecosystem secure.
