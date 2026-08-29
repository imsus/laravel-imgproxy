# Contribution Guide

Thank you for considering contributing to Laravel imgproxy! This project is
MIT-licensed, and contributions are welcome from anyone.

Before you start, please read our [Code of Conduct](../CODE_OF_CONDUCT.md).

## Reporting Bugs & Requesting Features

- **Bugs** — use the [Bug Report](ISSUE_TEMPLATE/bug.yml) template. Include the package, Laravel, and PHP versions, plus a minimal reproduction.
- **Features** — use the [Feature Request](ISSUE_TEMPLATE/feature_request.yml) template.
- For larger changes, open an issue first to discuss the approach before writing code.

## Process

1. Fork the project.
2. Create a feature branch.
3. Code, test, commit, and push.
4. Open a pull request describing your change.

## Development Setup

Clone your fork and install the dev dependencies:

```bash
composer install
```

## Validating Changes

Run the full validation suite before opening a pull request. It runs static
analysis, the formatting check, type coverage, and the unit tests:

```bash
composer test
```

You can run individual steps:

```bash
composer analyse      # PHPStan static analysis
composer lint:check   # check Pint formatting
composer lint         # auto-fix formatting with Pint
composer test:unit    # Pest unit tests (parallel)
composer test:types   # Pest type coverage (100% required)
```

The live imgproxy tests (against a real imgproxy in Docker) are gated behind
`IMGPROXY_URL`, `IMGPROXY_KEY`, and `IMGPROXY_SALT` and skip when they are unset.
CI never sets them.

## Guidelines

- Follow the existing code style (Pint). `composer lint` will format your changes.
- Keep each commit meaningful; write a coherent history.
- Rebase your branch to avoid merge conflicts.
- We follow [SemVer](https://semver.org/). Breaking changes require a major version bump and an upgrade note in `UPGRADING.md`.
- Keep type coverage at 100% (`composer test:types`).
- Add tests for any new observable behavior.

## Documentation

The documentation site is a VitePress app in `docs/`. Build it locally to verify changes:

```bash
pnpm run docs:build
```

- Keep the [API reference](../docs/reference/api.md) in sync with the public surface — every class, method, and signature documented there must match the code.
- **Version badges**: when a feature ships in a release, mark it in the docs with the VitePress Badge component so readers know when it was introduced. Put the badge on the feature's section heading and on its rows in API tables:

```markdown
## Materializing Processed Images <Badge type="tip" text="New in v2.1.0" />

| `toStorage` <Badge type="tip" text="v2.1.0" /> | ... |
```

`text` is the first release that includes the feature (`New in vX.Y.Z` on headings, `vX.Y.Z` in table cells). New features land in a minor release; bug fixes and internal changes do not get badges.

## Playground

The workbench app doubles as a live review playground. It renders real URLs and images through the package against a local imgproxy, so a human reviewer sees the actual output:

1. Point `workbench/.env` at an imgproxy server. Copy `workbench/.env.example` if needed — the defaults match the local Docker imgproxy used by the live integration tests (`docker run -d -p 8081:8080 -e IMGPROXY_KEY=0000... -e IMGPROXY_SALT=0000... imgproxy/imgproxy`, see the comments in the file).
2. Build and serve the workbench app on all interfaces with multiple PHP workers (the Docker imgproxy must reach the host, and the "Check all" status proxy blocks a worker on imgproxy while imgproxy fetches the sample from the workbench — one worker deadlocks):

```bash
composer build
PHP_CLI_SERVER_WORKERS=4 vendor/bin/testbench serve --host=0.0.0.0
```

3. The playground's sample image is served by the workbench app itself and fetched by imgproxy from inside Docker. Docker Desktop resolves `host.docker.internal` to the host; other Docker setups (e.g. Dory) need `PLAYGROUND_SOURCE` in `workbench/.env` set to the host's LAN IP (`ipconfig getifaddr en0` on macOS).
4. Open http://127.0.0.1:8000/ and walk the sections: source, URL builder demos, presets, Blade components, storage, commands, and validation.

The playground lives in `workbench/` (route, controller, view, the demo config with the `thumb` and `hero` presets, and the sample image) and is not shipped to package consumers.
