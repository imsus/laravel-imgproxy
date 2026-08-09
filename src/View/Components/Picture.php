<?php

declare(strict_types=1);

namespace LaravelImgproxy\LaravelImgproxy\View\Components;

use Illuminate\Contracts\View\View;
use InvalidArgumentException;
use LaravelImgproxy\LaravelImgproxy\Builder;
use LaravelImgproxy\LaravelImgproxy\Enums\Format;

/**
 * Renders a <picture> with one <source> per format and a fallback <img>.
 *
 * Every format except the last becomes a <source>; the last format is the
 * fallback <img>. Defaults to AVIF and WebP sources with a JPG fallback.
 */
final class Picture extends ImageComponent
{
    /**
     * Create a new component instance.
     *
     * @param  array<int, int>|string|null  $widths  Srcset width candidates, as an array or comma-separated string.
     * @param  array<int, int|float>|string|null  $dprs  Srcset DPR candidates, as an array or comma-separated string.
     * @param  array<int, Format|string>|string|null  $formats  Output formats, as an array or comma-separated string. The last format is the fallback <img>.
     */
    public function __construct(
        string $src = '',
        ?string $disk = null,
        ?string $path = null,
        ?string $preset = null,
        array|string|null $widths = null,
        array|string|null $dprs = null,
        ?string $sizes = null,
        bool $placeholder = false,
        ?string $alt = null,
        string $loading = 'lazy',
        public array|string|null $formats = ['avif', 'webp', 'jpg'],
    ) {
        parent::__construct($src, $disk, $path, $preset, $widths, $dprs, $sizes, $placeholder, $alt, $loading);
    }

    /**
     * The <source> elements: every format except the last fallback.
     *
     * @return list<array{type: string, srcset: string}>
     */
    public function sources(): array
    {
        $sources = [];

        foreach (array_slice($this->resolvedFormats(), 0, -1) as $format) {
            $builder = $this->formatBuilder($format);

            $sources[] = [
                'type' => $this->mimeType($format),
                'srcset' => $this->variants($builder) ?? $builder->url(),
            ];
        }

        return $sources;
    }

    /**
     * The format of the fallback <img>, the last of the configured formats.
     */
    public function fallbackFormat(): Format
    {
        $formats = $this->resolvedFormats();

        return $formats !== [] ? end($formats) : Format::Jpg;
    }

    /**
     * The URL for the fallback <img> src.
     */
    public function fallbackSrc(): string
    {
        return $this->placeholder ? $this->placeholderUrl() : $this->formatBuilder($this->fallbackFormat())->url();
    }

    /**
     * The srcset for the fallback <img>, or null.
     *
     * Only emitted with the placeholder enabled, so browsers without
     * <picture> support can still upgrade from the LQIP to the full image.
     */
    public function fallbackSrcset(): ?string
    {
        if (! $this->placeholder) {
            return null;
        }

        $builder = $this->formatBuilder($this->fallbackFormat());

        return $this->variants($builder) ?? $builder->url();
    }

    /**
     * The configured output formats, in render order.
     *
     * @return list<Format>
     */
    protected function resolvedFormats(): array
    {
        $items = $this->listItems($this->formats) ?? ['avif', 'webp', 'jpg'];

        return array_map(fn (string $item): Format => $this->resolveFormat($item), $items);
    }

    /**
     * A builder for the given output format.
     */
    protected function formatBuilder(Format $format): Builder
    {
        return $this->builder()->format($format);
    }

    /**
     * Resolve a format value to its enum.
     *
     * @throws InvalidArgumentException When the format is not supported.
     */
    protected function resolveFormat(Format|string $format): Format
    {
        if ($format instanceof Format) {
            return $format;
        }

        return Format::tryFrom(strtolower($format))
            ?? throw new InvalidArgumentException("The imgproxy component format [{$format}] is not supported.");
    }

    /**
     * The MIME type of the format, for the <source> type attribute.
     */
    protected function mimeType(Format $format): string
    {
        return match ($format) {
            Format::Jpg => 'image/jpeg',
            Format::Png => 'image/png',
            Format::Webp => 'image/webp',
            Format::Avif => 'image/avif',
            Format::Gif => 'image/gif',
            Format::Ico => 'image/x-icon',
            Format::Svg => 'image/svg+xml',
            Format::Bmp => 'image/bmp',
            Format::Tiff => 'image/tiff',
            Format::Heic => 'image/heic',
            Format::Jxl => 'image/jxl',
        };
    }

    public function render(): View
    {
        // Larastan cannot resolve package view namespaces at analysis time.
        return view('imgproxy::picture'); // @phpstan-ignore argument.type
    }
}
