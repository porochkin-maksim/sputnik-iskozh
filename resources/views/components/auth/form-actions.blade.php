@props([
    'class' => 'btn btn-success',
    'columnClass' => 'col-12 col-lg-8 offset-lg-4',
    'label' => null,
    'rowClass' => 'row mb-0 auth-form-actions',
    'type' => 'submit',
])

<div class="{{ $rowClass }}">
    <div class="{{ $columnClass }}">
        <div class="auth-form-actions__inner">
            <button type="{{ $type }}" class="{{ $class }} auth-form-actions__button">
                {{ $label ?? 'Submit' }}
            </button>

            @if(trim((string) $slot) !== '')
                <div class="auth-form-actions__aside">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>
