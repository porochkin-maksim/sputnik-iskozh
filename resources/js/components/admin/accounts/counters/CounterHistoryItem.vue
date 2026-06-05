<template>
    <counter-history-dialog
        :counter="counter"
        :history="history"
        @history-updated="$emit('historyUpdated')"
    />
</template>

<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue';

import CounterHistoryDialog from './counter-history-item/CounterHistoryDialog.vue';

defineProps({
    counter: {
        type   : Object,
        default: null,
    },
    history: {
        type   : Object,
        default: null,
    },
});

defineEmits(['historyUpdated']);
</script>
