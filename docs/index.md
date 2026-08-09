---
layout: home

hero:
  name: Laravel imgproxy
  text: Image processing for Laravel, without the busywork
  tagline: A fluent, typed, immutable builder for imgproxy — signed URLs, responsive images, and Storage integration, all with plain PHP.
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: View on GitHub
      link: https://github.com/imsus/laravel-imgproxy

features:
  - title: Immutable Builder
    details: Every method returns a new builder, so options compose safely and a base builder can be reused without surprises.
  - title: Typed Enums
    details: Resize types, gravity, output formats, and watermark positions as PHP enums — with the imgproxy string values always interchangeable.
  - title: HMAC Signing
    details: Hex key and salt signing with signature_size truncation, and unsigned URLs when no credentials are configured.
  - title: Multi-Instance
    details: A default instance plus any number of named instances, each with its own server, credentials, signature size, and encoding.
  - title: Blade Components
    details: Responsive srcsets, LQIP placeholders, DPR candidates, and AVIF/WebP picture elements — no JavaScript required.
  - title: Storage Integration
    details: Build sources from any Laravel Storage disk. Public disks yield plain URLs; private disks yield pre-signed temporary URLs.
---
