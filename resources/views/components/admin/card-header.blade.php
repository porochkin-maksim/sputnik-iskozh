<div class="d-flex align-items-center justify-content-between gap-2">
    <div class="flex-grow-1">
        {{ $title }}
    </div>

    @isset($tools)
        <div class="card-tools">
            {{ $tools }}
        </div>
    @endisset
</div>
