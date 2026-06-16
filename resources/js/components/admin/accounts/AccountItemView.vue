<template>
    <div class="row">
        <div class="col-xxl-6 col-12">
            <account-info-panel
                :account="account"
                :can-edit="canEdit"
                :can-save="canSave"
                :loading="loading"
                @save="saveAction"
                @update:cadastreNumber="formData.cadastreNumber = $event"
                @update:isInvoicing="formData.isInvoicing = $event"
                @update:number="formData.number = $event"
                @update:size="formData.size = $event"
            />
            <div class="mb-2" v-if="canUserView">
                <users-block :account="account"/>
            </div>
        </div>
        <div class="col-xxl-6 col-12 mb-2" v-if="canCounterView">
            <counters-block :account="account" />
        </div>
    </div>
    <div class="row" v-if="canInvoiceView">
        <div class="col-12 mb-2">
            <invoices-block :account="account" />
        </div>
        <div class="col-12 mb-2">
            <payments-block v-if="account?.id" :account-id="account.id" />
        </div>
    </div>
</template>

<script setup>
import CountersBlock          from './counters/CountersBlock.vue';
import InvoicesBlock          from './invoices/InvoicesBlock.vue';
import PaymentsBlock          from '../payments-manage/PaymentsBlock.vue';
import UsersBlock             from './users/UsersBlock.vue';
import AccountInfoPanel       from './account-item/AccountInfoPanel.vue';
import { useAccountItemView } from './account-item/useAccountItemView';

const props = defineProps({
    modelValue: {
        type    : Object,
        required: true,
    },
});

const {
          account,
          canCounterView,
          canEdit,
          canInvoiceView,
          canSave,
          canUserView,
          formData,
          loading,
          saveAction,
      } = useAccountItemView(props);
</script>
