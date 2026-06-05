<template>
    <div>
        <payments-header :is-loading="isLoading" />

        <payments-list
            v-if="!isLoading"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
        />

        <payment-dialog
            v-if="payment && (canEdit || canView) && (payment.actions.edit || payment.actions.view)"
            v-model:hide-dialog="hideDialog"
            v-model:period-id="periodId"
            v-model:show-dialog="showDialog"
            :accounts="accounts"
            :can-save="canSave"
            :errors="errors"
            :invoices="invoices"
            :invoices-loading="invoicesLoading"
            :period-id="periodId"
            :periods="periods"
            :payment="payment"
            :saving="saving"
            @account-changed="getInvoices"
            @clear-error="clearError"
            @file-updated="onFileUpdated"
            @hidden="closeAction"
            @save="saveAction"
        />
    </div>
</template>

<script setup>
import PaymentsHeader       from './payments-block/PaymentsHeader.vue';
import PaymentsList         from './PaymentsList.vue';
import PaymentDialog        from './payments-block/PaymentDialog.vue';
import { usePermissions }   from '@composables/usePermissions.js';
import { usePaymentsBlock } from './usePaymentsBlock.js';
import { computed }         from 'vue';

const props = defineProps({
    reload: {
        type   : Boolean,
        default: false,
    },
});

const emit    = defineEmits(['update:reload']);
const { has } = usePermissions();
const canEdit = computed(() => has('payments', 'edit'));
const canView = computed(() => has('payments', 'view'));

const {
          errors,
          clearError,
          reloadList,
          payment,
          selectedId,
          accounts,
          invoices,
          periods,
          periodId,
          isLoading,
          invoicesLoading,
          saving,
          showDialog,
          hideDialog,
          getInvoices,
          saveAction,
          closeAction,
          onFileUpdated,
          canSave,
      } = usePaymentsBlock(props, emit);
</script>
