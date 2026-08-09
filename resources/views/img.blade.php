@php
    // Compute the rendered URLs once; the srcset is null when no candidates exist.
    $src = $srcUrl();
    $srcset = $srcset();
@endphp

<img
    src="{{ $src }}"
    @if ($srcset !== null) srcset="{{ $srcset }}" @endif
    @if ($sizes !== null) sizes="{{ $sizes }}" @endif
    loading="{{ $loading }}"
    @if ($alt !== null) alt="{{ $alt }}" @endif
    {{ $attributes }}
>
