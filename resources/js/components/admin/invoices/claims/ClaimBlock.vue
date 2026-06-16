<template>
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="m-0">Услуги</h5>

            <div class="d-flex gap-2">
                <button
                    v-if="invoice.actions.claims.edit"
                    class="btn btn-success"
                    @click="makeAction"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Добавить услугу
                </button>
                <button
                    v-if="invoice.actions.claims.edit"
                    class="btn btn-warning"
                    @click="showPayAllDialog = true"
                >
                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                    Оплатить всё
                </button>
            </div>
        </div>

        <div class="card-body">

        <claims-list
            :invoice-id="invoice.id"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
            v-model:count="claimCount"
            @update:count="onUpdatedCount"
            @pay="onPayClaim"
        />

        <claim-editor
            :claim="claim"
            :can-save="canSave"
            :errors="errors"
            :hide-dialog="hideDialog"
            :loading="loading"
            :selected-id="selectedId"
            :services-select="servicesSelect"
            :show-dialog="showDialog"
            @clear-error="clearError"
            @cost-changed="onCostChanged"
            @hidden="closeAction"
            @save="saveAction"
            @service-id-changed="onServiceIdChanged"
            @tariff-changed="onTariffChanged"
        />

        <pay-all-dialog
            v-model:show="showPayAllDialog"
            :invoice-id="invoice.id"
            :account-id="invoice.accountId"
            @paid="onPayAllDone"
            @close="showPayAllDialog = false"
        />

        <view-dialog
            v-model:show="payClaimDialog.show"
            @hidden="payClaimDialog.show = false"
        >
            <template #title>Оплата услуги</template>

            <template #body>
                <p><strong>Услуга:</strong> {{ payClaimDialog.claim?.service }}</p>
                <p><strong>Осталось оплатить:</strong> {{ formatMoney(payClaimDialog.claim?.delta ?? 0) }}</p>
                <p v-if="claimPayBalance !== null">
                    <strong>Баланс участка: </strong>
                    <b :class="claimPayBalance >= 0 ? 'text-success' : 'text-danger'">
                        {{ formatMoney(claimPayBalance) }}
                    </b>
                </p>

                <div class="mb-3">
                    <custom-input
                        v-model="payClaimDialog.cost"
                        label="Сумма оплаты"
                        type="number"
                        step="0.01"
                        :max="payClaimDialog.claim?.delta ?? 0"
                    />
                </div>
            </template>

            <template #footer>
                <div class="d-flex justify-content-end w-100">
                    <button
                        class="btn btn-secondary me-2"
                        @click="payClaimDialog.show = false"
                        type="button"
                    >
                        Отмена
                    </button>
                    <button
                        class="btn btn-success"
                        :disabled="payClaimDialog.cost <= 0 || claimPayBalance === 0"
                        @click="confirmPayClaim"
                    >
                        <i class="fa fa-credit-card"></i> Оплатить
                    </button>
                </div>
            </template>
        </view-dialog>

    </div>
</div>
</template>

<script setup>
import {
    defineOptions,
    defineProps,
    ref,
}                        from 'vue';
import ViewDialog        from '@common/ViewDialog.vue';
import CustomInput       from '@common/form/CustomInput.vue';
import ClaimsList        from './ClaimsList.vue';
import ClaimEditor       from './claim-block/ClaimEditor.vue';
import PayAllDialog      from '../../payments-manage/PayAllDialog.vue';
import { useClaimBlock } from './claim-block/useClaimBlock';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import {
    ApiAdminPaymentManagePayClaim,
    ApiAdminPaymentManageAccountBalance,
} from '@api';

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
    reload : {
        type   : Boolean,
        default: false,
    },
    count  : {
        type   : Number,
        default: 0,
    },
});

const emit = defineEmits(['update:count', 'update:reload']);

const {
          canSave,
          claim,
          claimCount,
          clearError,
          closeAction,
          errors,
          hideDialog,
          loading,
          makeAction,
          onCostChanged,
          onServiceIdChanged,
          onTariffChanged,
          onUpdatedCount,
          reloadList,
          saveAction,
          selectedId,
          servicesSelect,
          showDialog,
      } = useClaimBlock(props, emit);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney }                               = useFormat();

const showPayAllDialog = ref(false);
const claimPayBalance  = ref(null);

const onPayAllDone = () => {
    showPayAllDialog.value = false;
    reloadList.value = true;
};

const payClaimDialog = ref({
    show : false,
    claim: null,
    cost : 0,
});

const onPayClaim = async (claim) => {
    payClaimDialog.value = {
        show : true,
        claim: claim,
        cost : parseFloat(claim.delta),
    };
    await loadClaimPayBalance();
};

const loadClaimPayBalance = async () => {
    const accountId = props.invoice.accountId;
    if ( ! accountId) {
        claimPayBalance.value = null;
        return;
    }
    try {
        const response = await ApiAdminPaymentManageAccountBalance(accountId);
        claimPayBalance.value = response.data?.balance ?? null;
    }
    catch {
        claimPayBalance.value = null;
    }
};

const confirmPayClaim = async () => {
    const claim = payClaimDialog.value.claim;
    const cost  = payClaimDialog.value.cost;
    if ( ! claim || cost <= 0) return;

    try {
        await ApiAdminPaymentManagePayClaim({}, {
            claim_id: claim.id,
            cost    : cost,
        });
        showInfo('Услуга оплачена');
        payClaimDialog.value.show = false;
        reloadList.value = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};
</script>
