@props([
    'href' => '#',
    'title' => '',
    'description' => '',
    'icon' => '',
    'color' => '',
])

<div {{ $attributes->merge(['class' => 'col-lg-4 col-md-6 col-12 pe-md-2 px-0']) }}>
    <a class="card request-item d-flex align-items-center justify-content-center p-3 d-flex flex-row"
       href="{{ $href }}">
        @if($icon)
            <div class="d-flex justify-content-center align-items-center me-3">
                <i class="fa {{ $icon }} fa-3x mb-2 {{ $color ? "text-{$color}" : "" }}"></i>
            </div>
        @endif
        <div class="d-flex flex-column justify-content-center align-items-center">
            <h3 class="{{ $color ? "text-{$color}" : "" }}">{{ $title }}</h3>
            <div class="text-center {{ $color ? "text-{$color}" : "" }}">
                {{ $description }}
            </div>
        </div>
    </a>
</div>