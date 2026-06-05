<template>
    <div class="invoice-item-view">
        <invoice-header-panel
            :can-account-view="canAccountView"
            :can-delete="canDelete"
            :can-edit="canEdit"
            :is-recalculating="isRecalculating"
            :local-invoice="localInvoice"
            @drop="dropAction"
            @recalc="recalcAction"
        />

        <invoice-summary-panel
            :actions="actions"
            :delta-class="deltaClass"
            :format-money="formatMoney"
            :local-invoice="localInvoice"
            :status-alert-class="statusAlertClass"
        />

        <invoice-claims-payments-panel
            :actions="actions"
            :claims-count="claimsCount"
            :invoice="props.invoice"
            :payments-count="paymentsCount"
            :reload="reload"
            @update:claimsCount="claimsCount = $event"
            @update:paymentsCount="paymentsCount = $event"
            @update:reload="reload = $event"
        />
    </div>
</template>

<script setup>
import {
    defineProps,
} from 'vue';

import { useInvoiceItemView }     from './invoice-item/useInvoiceItemView';
import InvoiceClaimsPaymentsPanel from './invoice-item/InvoiceClaimsPaymentsPanel.vue';
import InvoiceHeaderPanel         from './invoice-item/InvoiceHeaderPanel.vue';
import InvoiceSummaryPanel        from './invoice-item/InvoiceSummaryPanel.vue';

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
});

const {
          actions,
          canAccountView,
          canDelete,
          canEdit,
          claimsCount,
          deltaClass,
          dropAction,
          formatMoney,
          isRecalculating,
          localInvoice,
          recalcAction,
          reload,
          statusAlertClass,
          paymentsCount,
      } = useInvoiceItemView(props);
</script>
