<template>
    <loading-spinner
        v-if="loading"
        size="lg"
        color="primary"
        text="Загрузка платежей..."
        wrapper-class="py-5"
    />

    <template v-else>
        <div v-if="payments.length === 0" class="text-center text-secondary py-4">
            <i class="fa fa-credit-card fa-3x mb-3"></i>
            <p class="mb-0">Платежей пока нет</p>
        </div>

        <template v-else>
            <div class="table-responsive">
                <table class="table table-hover admin-table-firm">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Период</th>
                            <th>Назначение</th>
                            <th class="text-end">Сумма</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="payment in payments" :key="payment.id">
                            <td class="text-nowrap">{{ payment.date }}</td>
                            <td>{{ payment.periodName }}</td>
                            <td>{{ payment.name || payment.invoiceName }}</td>
                            <td class="text-end text-nowrap">{{ formatMoney(payment.cost) }}</td>
                        </tr>
                    </tbody>
                </table>
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
