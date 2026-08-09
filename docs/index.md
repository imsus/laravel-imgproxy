---
layout: home

hero:
  name: Laravel imgproxy
  text: A Laravel package for imgproxy
  tagline: Fast, secure image processing for Laravel applications
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/imsus/laravel-imgproxy

features:
  - title: Immutable Builder
    details: Fluent, immutable API with one typed method per imgproxy v4 processing option and terminal url() output.
  - title: Typed Enums
    details: PHP enums for resize type, gravity, output format, and watermark position with full string interchangeability.
  - title: HMAC Signing
    details: Hex key + salt signing with signature_size truncation; unsigned URLs when no credentials are configured.
  - title: Multi-Instance
    details: Default plus named instances, each with its own URL, credentials, signature size, and source encoding.
  - title: Blade Components
    details: Responsive srcsets, LQIP placeholders, DPR/width candidates, and AVIF/WebP picture elements out of the box.
  - title: Storage Integration
    details: Build sources from any Laravel Storage disk — public disks yield url(), private disks a pre-signed temporaryUrl().
---
