@props([
    'title' => null,
])

<div class="container auth-form-shell page-shell">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6">
            <div class="page-card auth-form-card">
                @if($title)
                    <div class="auth-form-card__header">
                        <h1 class="auth-form-card__title">{{ $title }}</h1>
                    </div>
                @endif

                <div class="auth-form-card__body">
                    {{ $slot }}
                </div>
            </div>
        </div>
</div>
