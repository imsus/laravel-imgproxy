@php
    // Compute the rendered URLs once; the fallback srcset is null unless the placeholder is enabled.
    $sources = $sources();
    $fallbackSrc = $fallbackSrc();
    $fallbackSrcset = $fallbackSrcset();
@endphp

<picture>
    @foreach ($sources as $source)
        <source srcset="{{ $source['srcset'] }}" type="{{ $source['type'] }}" @if ($sizes !== null) sizes="{{ $sizes }}" @endif>
    @endforeach
    <img
        src="{{ $fallbackSrc }}"
        @if ($fallbackSrcset !== null) srcset="{{ $fallbackSrcset }}" @endif
        @if ($sizes !== null) sizes="{{ $sizes }}" @endif
        loading="{{ $loading }}"
        @if ($alt !== null) alt="{{ $alt }}" @endif
        {{ $attributes }}
    >
</picture>
