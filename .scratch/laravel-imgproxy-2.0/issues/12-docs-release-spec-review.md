# Spec Review — 85d223d ("Release 2.0.0 docs and validation")

## (a) Missing or partial

**None.** Every acceptance checkbox in issue 12 is satisfied. README covers install, config, builder, options, presets, components, storage, commands, LQIP, and the Docker integration test — all with runnable examples. UPGRADING.md documents all breaking changes. CHANGELOG entry is present. CI runs `analyse`, `lint:check`, `test:types`, and `test:unit` (equivalent to `composer test`). Status is marked `done`.

## (b) Scope creep

**One minor item.** `.gitignore` adds `/bootstrap` and `/storage` (Testbench runtime artifacts). This is a reasonable cleanup but was not listed in issue 12's scope. Negligible impact.

## (c) Implemented but possibly wrong

**1. UPGRADING.md's Storage section is imprecise about the actual visibility logic.**

> UPGRADING.md: "Public/private detection is unchanged in spirit — public disks yield `url()`, private disks a pre-signed `temporaryUrl()` — with the visibility check now driven by `providesTemporaryUrls()` plus the disk's visibility config."

The actual code in `DiskUrl.php`:

```php
$temporary = $disk->providesTemporaryUrls()
    && ($disk->getConfig()['visibility'] ?? null) !== 'public';
```

This means: a disk with `providesTemporaryUrls()` is treated as **private unless its visibility is explicitly set to `'public'`**. If `visibility` is omitted (the Laravel default for most drivers), the disk is treated as private. This is a meaningful behavioral detail — it inverts the default for disks that omit visibility config. The phrase "visibility config" in UPGRADING.md understates the risk: users with disks that have no explicit visibility key (most default S3/local configs) will now get `temporaryUrl()` where they previously got `url()`. The UPGRADING note should explicitly warn about this.

## Overall

The commit is clean. The README, UPGRADING, CHANGELOG, CI matrix, test hermeticity fixes, and `.gitignore` all align with the ticket and spec. The only actionable item is tightening the UPGRADING Storage visibility explanation to warn users that disks without an explicit `'visibility' => 'public'` config key will now produce pre-signed URLs.
