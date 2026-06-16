<template>
    <payments-block
        :reload="reload"
        @update:reload="$emit('update:reload', $event)"
    />
</template>

<script setup>
import PaymentsBlock from '../payments-manage/PaymentsBlock.vue';
const props = defineProps({
    reload: {
        type   : Boolean,
        default: false,
    },
});
const emit = defineEmits(['update:reload']);
</script>
