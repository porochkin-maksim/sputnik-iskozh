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
            :balance="balance"
            :can-edit="canEdit"
            :delta-class="deltaClass"
            :format-money="formatMoney"
            :local-invoice="localInvoice"
            :status-alert-class="statusAlertClass"
            @edit-rounding="openRoundingEdit"
        />

        <view-dialog
            v-model:show="showRoundingModal"
            modal-class="modal-sm"
        >
            <template #title>
                Коррекция округления
            </template>
            <template #body>
                <div class="mb-3">
                    <label class="form-label">
                        Округление ({{ formatMoney(localInvoice.detailCost?.rounding ?? 0) }})
                    </label>
                    <input
                        v-model="roundingValue"
                        type="number"
                        step="0.01"
                        class="form-control"
                    >
                </div>
            </template>
            <template #footer>
                <button
                    class="btn btn-secondary"
                    @click="showRoundingModal = false"
                >
                    Отмена
                </button>
                <button
                    class="btn btn-primary"
                    @click="updateRoundingAction"
                >
                    Сохранить
                </button>
            </template>
        </view-dialog>

        <invoice-claims-payments-panel
            :actions="actions"
            :claims-count="claimsCount"
            :invoice="props.invoice"
            :reload="reload"
            @update:claimsCount="claimsCount = $event"
            @update:reload="reload = $event"
        />
    </div>
</template>

<script setup>
import {
    defineProps,
} from 'vue';

import { useInvoiceItemView }     from './invoice-item/useInvoiceItemView';
import ViewDialog                 from '@common/ViewDialog.vue';
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
          balance,
          canAccountView,
          canDelete,
          canEdit,
          claimsCount,
          deltaClass,
          dropAction,
          formatMoney,
          isRecalculating,
          localInvoice,
          openRoundingEdit,
          recalcAction,
          reload,
          roundingValue,
          showRoundingModal,
          statusAlertClass,
          updateRoundingAction,
      } = useInvoiceItemView(props);
</script>
