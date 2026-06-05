<template>
    <div v-if="normalizedErrors?.length" class="invalid-feedback d-block">
        <ul class="list-unstyled mb-0">
            <li
                v-for="(error, index) in normalizedErrors"
                :key="index"
            >
                {{ error }}
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    errors: [String, Array],
});

const normalizedErrors = computed(() => {
    if (!props.errors || props.errors === '') {
        return null;
    }
    return Array.isArray(props.errors) ? props.errors : [props.errors];
});
</script>
