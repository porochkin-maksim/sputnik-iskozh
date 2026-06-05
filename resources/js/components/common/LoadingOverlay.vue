<template>
    <div class="loading-overlay" :class="{ absolute: absolute }">
        <loading-spinner
            :size="size"
            :color="color"
            :text="text"
            :show-text="showText"
            :wrapper-class="'p-4'"
        />
    </div>
</template>

<script setup>
import LoadingSpinner from '@common/LoadingSpinner.vue';

defineProps({
    absolute: {
        type   : Boolean,
        default: false,
    },
    size    : {
        type   : String,
        default: 'md',
    },
    color   : {
        type   : String,
        default: 'primary',
    },
    text    : {
        type   : String,
        default: 'Загрузка...',
    },
    showText: {
        type   : Boolean,
        default: true,
    },
});
</script>
