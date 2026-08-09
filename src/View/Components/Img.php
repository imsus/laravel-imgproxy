<?php

declare(strict_types=1);

namespace Imsus\LaravelImgproxy\View\Components;

use Illuminate\Contracts\View\View;

/**
 * Renders an <img> with a srcset built from width or DPR candidates.
 *
 * The src attribute carries the LQIP placeholder URL when the placeholder
 * flag is set, with the full-size image reachable through the srcset.
 */
final class Img extends ImageComponent
{
    /**
     * The URL for the img src: the LQIP placeholder when enabled, else the full image.
     */
    public function srcUrl(): string
    {
        return $this->placeholder ? $this->placeholderUrl() : $this->builder()->url();
    }

    /**
     * The srcset attribute value, or null when there are no candidates.
     *
     * With the placeholder enabled and no width or DPR candidates, the full
     * image stays reachable through a single 1x candidate.
     */
    public function srcset(): ?string
    {
        $builder = $this->builder();

        return $this->variants($builder) ?? ($this->placeholder ? $builder->url() : null);
    }

    public function render(): View
    {
        return view('imgproxy::img');
    }
}
