@props([
    'items' => [],
    'columns' => 'col-lg-4 col-md-6 col-12',
    ])

<div {{ $attributes->merge(['class' => 'row requests-block w-100 ms-0']) }}>
    @foreach($items as $item)
        @include('public.partial.request-card', [
            'href' => $item['href'] ?? '',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'icon' => $item['icon'] ?? '',
            'color' => $item['color'] ?? '',
        ])
    @endforeach
</div>