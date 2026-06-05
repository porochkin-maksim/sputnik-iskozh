@props([
    'class' => 'mb-3',
    'index' => false,
])

<div class="public-social-strip {{ $class }}">
    <div class="social social-contacts d-flex {{ $index ? 'social-index' : '' }} {{ $index ? 'public-social-strip-bordered' : '' }}">
        @include('layouts.partial.social')
    </div>
</div>
