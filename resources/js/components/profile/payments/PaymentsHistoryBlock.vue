<template>
    <loading-spinner
        v-if="loading"
        size="lg"
        color="primary"
        text="Загрузка платежей..."
        wrapper-class="py-5"
    />

    <template v-else>
        <div class="page-card profile-balance-card mb-3">
            <div class="profile-balance-card__label">Баланс</div>
            <div class="profile-balance-card__value" :class="balance > 0 ? 'text-success' : 'text-secondary'">
                {{ formatMoney(balance) }}
            </div>
        </div>

        <div v-if="payments.length === 0" class="profile-empty-state">
            <hr>
            <h6 class="text-center text-secondary m-0"><i>платежей нет...</i></h6>
        </div>

        <template v-else>
            <div class="profile-payments-list">
                <div v-for="payment in payments" :key="payment.id" class="page-card profile-payment-card">
                    <div class="profile-payment-card__header">
                        <div class="profile-payment-card__title">
                            <span class="profile-payment-card__label">Дата</span>
                            <span class="profile-payment-card__value">{{ payment.date }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="profile-payment-card__amount">{{ formatMoney(payment.cost) }}</div>
                        </div>
                    </div>
                    <div class="profile-payment-card__meta">
                        <div class="profile-payment-chip">
                            <span class="profile-payment-chip__label" v-if="payment.periodName">Период</span>
                            <span class="profile-payment-chip__value">{{ payment.periodName }}</span>
                        </div>
                        <div class="profile-payment-chip">
                            <span class="profile-payment-chip__label" v-if="payment.name || payment.invoiceName">Назначение</span>
                            <span class="profile-payment-chip__value">{{ payment.name || payment.invoiceName }}</span>
                        </div>
                        <div class="profile-payment-chip">
                            <span v-if="!payment.verified" class="badge bg-warning text-dark">На проверке</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="hasMore" class="text-center mt-3">
                <button
                    class="btn btn-outline-primary btn-sm"
                    :disabled="loadingMore"
                    @click="loadMore"
                >
                    <i v-if="loadingMore" class="fa fa-spinner fa-spin me-1"></i>
                    Показать ещё
                </button>
            </div>
        </template>
    </template>
</template>

<script setup>
import { ref, computed } from 'vue';
import LoadingSpinner from '@common/LoadingSpinner.vue';
import { useResponseError } from '@composables/useResponseError';
import { ApiProfilePaymentsList } from '@api';

const { parseResponseErrors } = useResponseError();

const loading     = ref(true);
const loadingMore = ref(false);
const payments    = ref([]);
const total       = ref(0);
const balance     = ref(0);
const limit       = ref(20);
const offset      = ref(0);

const hasMore = computed(() => payments.value.length < total.value);

function formatMoney(amount) {
    if (amount === null || amount === undefined) return '';
    return Number(amount).toLocaleString('ru-RU', {
        style                : 'currency',
        currency             : 'RUB',
        minimumFractionDigits: 2,
    });
}

async function loadData() {
    loading.value = true;
    try {
        const response = await ApiProfilePaymentsList({}, {
            limit: limit.value,
            skip : 0,
        });
        payments.value = response.data.payments;
        total.value    = response.data.total;
        balance.value  = response.data.balance || 0;
        limit.value    = response.data.limit || 20;
        offset.value   = payments.value.length;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
}

async function loadMore() {
    loadingMore.value = true;
    try {
        const response = await ApiProfilePaymentsList({}, {
            limit: limit.value,
            skip : offset.value,
        });
        payments.value = [...payments.value, ...response.data.payments];
        offset.value   = payments.value.length;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loadingMore.value = false;
    }
}

loadData();
</script>
