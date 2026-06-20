<template>
    <counter-editor-dialog
        :account="account"
        :counter="counter"
        :show-form="showForm"
        @counter-updated="$emit('counterUpdated')"
    />
</template>

<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue';

import CounterEditorDialog from './counter-item/CounterEditorDialog.vue';

defineProps({
    account : {
        type   : Object,
        default: null,
    },
    counter : {
        type   : Object,
        default: null,
    },
    showForm: {
        type   : Boolean,
        default: false,
    },
});

defineEmits(['counterUpdated']);
</script>
