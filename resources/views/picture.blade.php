<picture>
    @foreach($buildUrls() as $format => $url)
        @if($loop->last)
            <img src="{{ $url }}"
                @if(isset($alt)) alt="{{ $alt }}" @endif
                @if($lazy) loading="lazy" @endif
                @if(isset($sizes)) sizes="{{ $sizes }}" @endif
                {{ $attributes->except(['src', 'alt', 'width', 'height', 'resizeType', 'formats', 'quality', 'dpr', 'gravity', 'lazy', 'sizes']) }}
            >
        @else
            <source srcset="{{ $url }}" type="image/{{ $format }}">
        @endif
    @endforeach
</picture>
