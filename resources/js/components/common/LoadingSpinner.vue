<template>
    <div :class="['text-center', wrapperClass]" :style="wrapperStyle">
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
    // Размер спиннера
    size: {
        type     : String,
        default  : 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    // Цвет спиннера
    color: {
        type   : String,
        default: 'primary', // primary, secondary, success, danger, warning, info, light, dark
    },
    // Текст под спиннером
    text: {
        type   : String,
        default: 'Загрузка...',
    },
    // Показывать ли текст
    showText: {
        type   : Boolean,
        default: true,
    },
    // Дополнительные CSS классы для контейнера
    wrapperClass: {
        type   : String,
        default: 'py-5',
    },
    // Стили для контейнера
    wrapperStyle: {
        type   : [Object, String],
        default: () => ({}),
    },
    // Стили для спиннера
    spinnerStyle: {
        type   : [Object, String],
        default: () => ({}),
    },
    // Класс для текста
    textClass: {
        type   : String,
        default: 'text-muted',
    },
});

// Размеры спиннера
const sizeClass = computed(() => {
    const sizes = {
        sm: 'spinner-border-sm',
        md: '',
        lg: '',
    };
    return sizes[props.size] || '';
});

// Цвет спиннера (Bootstrap)
const colorClass = computed(() => `text-${props.color}`);
</script>