<template>
    <claim-block
        v-if="actions.claims?.view"
        :invoice="invoice"
        :count="claimsCount"
        :reload="reload"
        @update:count="$emit('update:claimsCount', $event)"
        @update:reload="$emit('update:reload', $event)"
    />

    <div v-if="actions.claims?.view && actions.payments?.view" class="border-top my-3"></div>

    <payments-block
        v-if="actions.payments?.view"
        :invoice="invoice"
        :count="paymentsCount"
        :reload="reload"
        @update:count="$emit('update:paymentsCount', $event)"
        @update:reload="$emit('update:reload', $event)"
    />
</template>

<script setup>
import ClaimBlock    from '../claims/ClaimBlock.vue';
import PaymentsBlock from '../payments/PaymentsBlock.vue';

defineProps({
    actions      : { type: Object, required: true },
    claimsCount  : { type: Number, required: true },
    invoice      : { type: Object, required: true },
    paymentsCount: { type: Number, required: true },
    reload       : { type: Boolean, required: true },
});

defineEmits(['update:claimsCount', 'update:paymentsCount', 'update:reload']);
</script>
