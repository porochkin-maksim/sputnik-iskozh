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
import LoadingSpinner from './LoadingSpinner.vue';

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

<style scoped>
.loading-overlay {
    position        : fixed;
    top             : 0;
    left            : 0;
    width           : 100%;
    height          : 100%;
    background      : rgba(255, 255, 255, 0.8);
    display         : flex;
    align-items     : center;
    justify-content : center;
    z-index         : 9999;
}

.loading-overlay.absolute {
    position : absolute;
}
</style>