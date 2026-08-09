# Contribution Guide

Thank you for considering contributing to Laravel imgproxy! Please review the following guidelines before submitting a pull request.

For significant changes, please open an issue first so we can discuss the approach.

## Process

1. Fork the project
2. Create a new branch
3. Code, test, commit, and push
4. Open a pull request detailing your changes

## Guidelines

- Ensure the coding style passes by running `composer lint`.
- Send a coherent commit history, making sure each commit in your pull request is meaningful.
- You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
- Please remember that we follow [SemVer](http://semver.org/).

## Setup

Clone your fork, then install the dev dependencies:

```bash
composer install
```

## Lint

Lint your code:

```bash
composer lint
```

## Tests

Run all tests:

```bash
composer test
```

## Playground

The workbench app doubles as a live review playground. It renders real URLs and images through the package against a local imgproxy, so a human reviewer sees the actual output:

1. Point `workbench/.env` at an imgproxy server. Copy `workbench/.env.example` if needed — the defaults match the local Docker imgproxy used by the live integration tests (`docker run -d -p 8081:8080 -e IMGPROXY_KEY=0000... -e IMGPROXY_SALT=0000... imgproxy/imgproxy`, see the comments in the file).
2. Build and serve the workbench app:

```bash
composer build
composer serve
```

3. Open http://127.0.0.1:8000/ and walk the sections: source, URL builder demos, presets, Blade components, storage, commands, and validation.

The playground lives in `workbench/` (route, controller, view, and the demo config with the `thumb` and `hero` presets) and is not shipped to package consumers.
