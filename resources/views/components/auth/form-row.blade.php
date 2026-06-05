@props([
    'for' => null,
    'label' => null,
    'error' => null,
    'labelClass' => 'col-12 col-lg-4 col-form-label auth-form-row__label',
    'inputClass' => 'col-12 col-lg-8 auth-form-row__field',
    'rowClass' => 'row g-2 align-items-center auth-form-row',
])

<div class="{{ $rowClass }}">
    @if($label)
        <label @if($for) for="{{ $for }}" @endif class="{{ $labelClass }}">
            <span class="auth-form-row__label-text">{{ $label }}</span>
        </label>
    @endif

    <div class="{{ $inputClass }}">
        {{ $slot }}

        @if($error)
            <span class="invalid-feedback" role="alert">
                <strong>{{ $error }}</strong>
            </span>
        @endif
    </div>
</div>
