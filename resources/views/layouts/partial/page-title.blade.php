@props([
    'href' => null,
    'icon' => null,
    'iconColor' => null,
    'text' => '',
    'class' => 'page-title',
])

<h1 class="{{ $class }}">
    @if($href)
        <a href="{{ $href }}"
           @class(['text-decoration-none' => true])>
            @if($icon)
                <i class="fa {{ $icon }}{{ $iconColor ? ' text-' . $iconColor : '' }} me-1"></i>
            @endif
            {{ $text }}
        </a>
    @else
        @if($icon)
            <i class="fa {{ $icon }}{{ $iconColor ? ' text-' . $iconColor : '' }} me-1"></i>
        @endif
        {{ $text }}
    @endif
</h1>
