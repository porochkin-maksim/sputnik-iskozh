<template>
    <claim-block
        v-if="actions.claims?.view"
        :invoice="invoice"
        :count="claimsCount"
        :reload="reload"
        @update:count="$emit('update:claimsCount', $event)"
        @update:reload="$emit('update:reload', $event)"
    />
</template>

<script setup>
import ClaimBlock from '../claims/ClaimBlock.vue';

defineProps({
    actions    : { type: Object, required: true },
    claimsCount: { type: Number, required: true },
    invoice    : { type: Object, required: true },
    reload     : { type: Boolean, required: true },
});

defineEmits(['update:claimsCount', 'update:reload']);
</script>
