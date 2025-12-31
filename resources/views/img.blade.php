<img src="{{ $buildUrl() }}"
    @if(isset($alt)) alt="{{ $alt }}" @endif
    @if($lazy) loading="lazy" @endif
    @if(isset($sizes)) sizes="{{ $sizes }}" @endif
    {{ $attributes->except(['src', 'alt', 'width', 'height', 'resizeType', 'format', 'quality', 'dpr', 'gravity', 'lazy', 'sizes']) }}
>
