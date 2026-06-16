<template>
    <view-dialog v-model:show="showValue" @hidden="$emit('close')">
        <template #title>Оплата всех услуг счёта</template>

        <template #body>
            <div v-if="loading" class="text-center py-3">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Загрузка информации...</p>
            </div>

            <div v-else-if="info" class="mb-3">
                <div class="alert" :class="info.canCover ? 'alert-success' : 'alert-warning'">
                    <p class="mb-1">
                        <strong>Сумма услуг к оплате:</strong>
                        {{ formatMoney(info.totalClaims) }}
                    </p>
                    <p class="mb-1">
                        <strong>Баланс участка:</strong>
                        {{ formatMoney(balance) }}
                    </p>
                    <p class="mb-0">
                        <strong>Будет оплачено:</strong>
                        {{ formatMoney(willPay) }}
                    </p>
                </div>

                <div v-if="!canCover && balance > 0" class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Баланса недостаточно для полной оплаты. Будет оплачена доступная сумма.
                </div>
                <div v-if="balance <= 0" class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    Нет нераспределённых средств для оплаты.
                </div>
            </div>
        </template>

        <template #footer>
            <div class="d-flex justify-content-end w-100">
                <button
                    class="btn btn-secondary me-2"
                    @click="$emit('close')"
                    type="button"
                >
                    Отмена
                </button>
                <button
                    class="btn btn-success"
                    :disabled="!canPay || loading"
                    @click="confirm"
                >
                    <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                    Оплатить
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import ViewDialog               from '@common/ViewDialog.vue';
import { useFormat }            from '@composables/useFormat';
import { useResponseError }     from '@composables/useResponseError';
import {
    ApiAdminPaymentManageCanPayAll,
    ApiAdminPaymentManagePayAll,
    ApiAdminPaymentManageAccountBalance,
}                               from '@api';

const props = defineProps({
    show     : { type: Boolean, default: false },
    invoiceId: { type: Number, default: null },
    accountId: { type: Number, default: null },
});

const emit = defineEmits(['update:show', 'close', 'paid']);

const { formatMoney }                   = useFormat();
const { parseResponseErrors, showInfo } = useResponseError();

const showValue = computed({
    get: () => props.show,
    set: (val) => emit('update:show', val),
});

const loading = ref(false);
const info    = ref(null);
const balance = ref(0);

const canCover = computed(() => info.value && balance.value >= info.value.totalClaims);
const willPay  = computed(() => info.value ? Math.min(balance.value, info.value.totalClaims) : 0);
const canPay   = computed(() => balance.value > 0);

watch(() => props.show, async (val) => {
    if (val && props.invoiceId) {
        await loadInfo();
    }
});

const loadInfo = async () => {
    loading.value = true;
    info.value    = null;
    balance.value = 0;
    try {
        const [infoResp, balanceResp] = await Promise.all([
            ApiAdminPaymentManageCanPayAll(props.invoiceId),
            props.accountId ? ApiAdminPaymentManageAccountBalance(props.accountId) : Promise.resolve({ data: { balance: 0 } }),
        ]);
        info.value    = infoResp.data;
        balance.value = balanceResp?.data?.balance ?? 0;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

const confirm = async () => {
    if ( ! canPay.value) return;

    loading.value = true;
    try {
        await ApiAdminPaymentManagePayAll(props.invoiceId);
        showInfo('Услуги оплачены');
        emit('paid');
        emit('close');
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};
</script>
