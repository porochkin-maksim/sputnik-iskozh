<template>
    <div class="custom-calendar__header d-flex align-items-center justify-content-between mb-2">
        <button class="custom-calendar__nav-button btn btn-sm btn-outline-secondary"
                @click="$emit('prev')"
                type="button">
            &lt;
        </button>
        <span class="custom-calendar__month-name fw-medium"
              @click="$emit('toggle-month-year')">
            {{ monthName }} {{ year }}
        </span>
        <button class="custom-calendar__nav-button btn btn-sm btn-outline-secondary"
                @click="$emit('next')"
                type="button">
            &gt;
        </button>
    </div>
</template>

<script setup>
defineProps({
    monthName: String,
    year     : Number,
});
defineEmits(['prev', 'next', 'toggle-month-year']);
</script>