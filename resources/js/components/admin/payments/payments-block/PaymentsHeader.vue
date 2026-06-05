<template>
    <div>
        <h5>Новые платежи</h5>

        <loading-spinner
            v-if="isLoading"
            size="md"
            color="primary"
            text="Загрузка платежей..."
            wrapper-class="py-4"
        />
    </div>
</template>

<script setup>
import LoadingSpinner from '@common/LoadingSpinner.vue';

defineProps({
    isLoading: {
        type   : Boolean,
        default: false,
    },
});
</script>
