@props([
    'items' => [],
    'columns' => 'col-lg-4 col-md-6 col-12',
    ])

<div {{ $attributes->merge(['class' => 'row requests-block w-100 ms-0 gy-2']) }}>
    @foreach($items as $item)
        @include('partials.public.request-card', [
            'href' => $item['href'] ?? '',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'icon' => $item['icon'] ?? '',
            'color' => $item['color'] ?? '',
        ])
    @endforeach
</div>
