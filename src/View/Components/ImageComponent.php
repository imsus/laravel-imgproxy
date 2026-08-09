<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy\View\Components;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;
use Imsus\LaravelImgproxy\Builder;
use Imsus\LaravelImgproxy\DiskUrl;
use Imsus\LaravelImgproxy\Enums\Format;
use InvalidArgumentException;

/**
 * Shared behavior for the imgproxy Blade components.
 *
 * Both components render a source URL or a Storage disk + path through the
 * URL builder, with a named preset composed before per-URL overrides and a
 * srcset built from width candidates, DPR candidates, or neither. The
 * `placeholder` flag pairs an LQIP placeholder with the full-size image.
 *
 * Width and DPR candidates are mutually exclusive: a single srcset cannot
 * mix `w` and `x` descriptors.
 */
abstract class ImageComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @param  string  $src  Source image URL, or empty when a disk and path are given.
     * @param  string|null  $disk  Storage disk name, paired with the path.
     * @param  string|null  $path  Source path on the disk.
     * @param  string|null  $preset  Named preset from config, composed before per-URL overrides.
     * @param  array<int, int>|string|null  $widths  Srcset width candidates, as an array or comma-separated string.
     * @param  array<int, int|float>|string|null  $dprs  Srcset DPR candidates, as an array or comma-separated string.
     * @param  string|null  $sizes  The sizes attribute value.
     * @param  bool  $placeholder  Pair an LQIP placeholder with the full-size image.
     * @param  string|null  $alt  Alt text for accessibility.
     * @param  string  $loading  The loading attribute value, lazy by default.
     *
     * @throws InvalidArgumentException When the source is missing, widths and DPRs are combined, or a value is invalid.
     */
    public function __construct(
        public string $src = '',
        public ?string $disk = null,
        public ?string $path = null,
        public ?string $preset = null,
        public array|string|null $widths = null,
        public array|string|null $dprs = null,
        public ?string $sizes = null,
        public bool $placeholder = false,
        public ?string $alt = null,
        public string $loading = 'lazy',
    ) {
        $this->validate();
    }

    /**
     * Validate the component attributes.
     *
     * @throws InvalidArgumentException When the source is missing, widths and DPRs are combined, or a value is invalid.
     */
    protected function validate(): void
    {
        if ($this->disk !== null || $this->path !== null) {
            if ($this->disk === null || $this->path === null) {
                throw new InvalidArgumentException('The imgproxy component requires both a disk and a path.');
            }

            if ($this->src !== '') {
                throw new InvalidArgumentException('The imgproxy component accepts a source URL or a disk and path, not both.');
            }
        } elseif ($this->src === '') {
            throw new InvalidArgumentException('The imgproxy component requires a source URL or a disk and path.');
        }

        if ($this->normalizedWidths() !== null && $this->normalizedDprs() !== null) {
            throw new InvalidArgumentException('The imgproxy component cannot combine widths and DPRs in one srcset.');
        }
    }

    /**
     * The URL builder for the component's source, with the preset applied.
     *
     * @throws InvalidArgumentException When the source is not configured.
     */
    protected function builder(): Builder
    {
        if ($this->disk !== null && $this->path !== null) {
            $builder = imgproxy()->url(DiskUrl::resolve(Storage::disk($this->disk), $this->path));
        } elseif ($this->src !== '') {
            $builder = imgproxy()->url($this->src);
        } else {
            throw new InvalidArgumentException('The imgproxy component requires a source URL or a disk and path.');
        }

        return $this->preset !== null ? $builder->preset($this->preset) : $builder;
    }

    /**
     * A srcset of width or DPR candidates for the given builder.
     *
     * Width candidates carry `w` descriptors, DPR candidates `x` descriptors.
     */
    protected function variants(Builder $builder): ?string
    {
        $widths = $this->normalizedWidths();

        if ($widths !== null) {
            return implode(', ', array_map(
                fn (int $width): string => $builder->width($width)->url()." {$width}w",
                $widths,
            ));
        }

        $dprs = $this->normalizedDprs();

        if ($dprs !== null) {
            return implode(', ', array_map(
                fn (int|float $dpr): string => $builder->dpr($dpr)->url().' '.$dpr.'x',
                $dprs,
            ));
        }

        return null;
    }

    /**
     * The LQIP placeholder URL for the component's source.
     */
    protected function placeholderUrl(): string
    {
        return $this->builder()->placeholder()->url();
    }

    /**
     * The normalized width candidates, or null when none are configured.
     *
     *
     * @return list<int>|null
     *
     * @throws InvalidArgumentException When a width is not a positive integer.
     */
    protected function normalizedWidths(): ?array
    {
        $items = $this->listItems($this->widths);

        if ($items === null) {
            return null;
        }

        $widths = [];

        foreach ($items as $item) {
            if (! is_numeric($item) || (int) $item != (float) $item || (int) $item <= 0) {
                throw new InvalidArgumentException("The imgproxy component width [{$item}] must be a positive integer.");
            }

            $widths[] = (int) $item;
        }

        return $widths;
    }

    /**
     * The normalized DPR candidates, or null when none are configured.
     *
     *
     * @return list<int|float>|null
     *
     * @throws InvalidArgumentException When a DPR is not a positive number.
     */
    protected function normalizedDprs(): ?array
    {
        $items = $this->listItems($this->dprs);

        if ($items === null) {
            return null;
        }

        $dprs = [];

        foreach ($items as $item) {
            if (! is_numeric($item) || (float) $item <= 0) {
                throw new InvalidArgumentException("The imgproxy component DPR [{$item}] must be a positive number.");
            }

            $dprs[] = str_contains($item, '.') ? (float) $item : (int) $item;
        }

        return $dprs;
    }

    /**
     * Split a list attribute into its items, or null when absent or empty.
     *
     * Accepts a native array or a comma-separated string. Format enums in an
     * array are converted to their string values.
     *
     * @param  array<int, int|float|string|Format>|string|null  $value
     * @return list<string>|null
     */
    protected function listItems(array|string|null $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $items = is_string($value) ? explode(',', $value) : $value;

        $items = array_map(
            fn (mixed $item): string => $item instanceof Format ? $item->value : trim((string) $item),
            $items,
        );

        $items = array_values(array_filter($items, fn (string $item): bool => $item !== ''));

        return $items === [] ? null : $items;
    }
}
