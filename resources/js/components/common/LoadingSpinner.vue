<template>
    <div :class="['loading-spinner', 'text-center', wrapperClass]" :style="wrapperStyle">
        <div
            class="spinner-border"
            :class="[colorClass, sizeClass]"
            role="status"
            :style="spinnerStyle"
        >
            <span class="visually-hidden">{{ text }}</span>
        </div>
        <p v-if="text && showText" :class="['mt-2', textClass]">{{ text }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type     : String,
        default  : 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    color: {
        type   : String,
        default: 'primary',
    },
    text: {
        type   : String,
        default: 'Загрузка...',
    },
    showText: {
        type   : Boolean,
        default: true,
    },
    wrapperClass: {
        type   : String,
        default: 'py-4',
    },
    wrapperStyle: {
        type   : [Object, String],
        default: () => ({}),
    },
    spinnerStyle: {
        type   : [Object, String],
        default: () => ({}),
    },
    textClass: {
        type   : String,
        default: 'text-muted',
    },
});

const sizeClass = computed(() => {
    const sizes = {
        sm: 'spinner-border-sm',
        md: '',
        lg: '',
    };
    return sizes[props.size] || '';
});

const colorClass = computed(() => `text-${props.color}`);
</script>
