# Security Policy

## Supported Versions

The following versions of this package are currently supported with security updates:

| Version | Supported |
| ------- | --------- |
| v1.x    | ✅         |
| v0.x    | ✅         |

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please follow these steps:

1. **Do not** open a public issue on GitHub
2. Email your report to: **abc.imams@gmail.com**
3. Include a detailed description of the vulnerability
4. Include steps to reproduce the issue
5. Include any relevant code samples or proof-of-concept

## Disclosure Process

1. Once we receive your security report, we will acknowledge it within 24 hours
2. Our security team will review and validate the report
3. If confirmed, we will:
   - Develop a fix for the vulnerability
   - Release a patched version
   - Publish a security advisory on GitHub
4. We follow responsible disclosure: fixes are released before full details are disclosed

## Scope

This security policy applies to:
- The `imsus/laravel-imgproxy` package code
- The `imgproxy()` helper function and its methods
- The ImgProxy facade
- Configuration loading and HMAC signature generation

This policy does **not** apply to:
- The ImgProxy server itself (please report to imgproxy.net)
- User-provided source URLs or watermark URLs
- Your application's implementation of this package
