<img src="{{ $buildUrl() }}"
    src="{{ $buildUrl() }}"
    @if($lazy) loading="lazy" @endif
    {{ $attributes->except(['src', 'width', 'height', 'resizeType', 'format', 'quality', 'dpr', 'gravity', 'lazy']) }}
>
