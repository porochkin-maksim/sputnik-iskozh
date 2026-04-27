<template>
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="m-0">Счета</h5>
        </div>

        <div class="card-body">
            <loading-spinner
                v-if="loading && invoices.length === 0"
                size="lg"
                color="primary"
                text="Загрузка счетов..."
                wrapper-class="py-5"
            />

            <template v-else>
                <table class="table table-sm table-striped table-bordered mb-0"
                       v-if="invoices && invoices.length">
                    <thead>
                    <tr class="text-center">
                        <th>№</th>
                        <th>Название/Тип</th>
                        <th>Период</th>
                        <th>Стоимость</th>
                        <th>Оплачено</th>
                        <th>Долг</th>
                        <th>Обновлён</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="invoice in invoices" :key="invoice.id"
                        class="text-center align-middle"
                        :class="[invoice.isPaid ? 'table-success' : '', invoice.cost === 0 ? 'table-warning' : '', invoice.advance ? 'fw-bold' : '']">
                        <td class="text-end">
                            <a :href="invoice.viewUrl">
                                {{ invoice.id }}
                            </a>
                        </td>
                        <td>{{ invoice.displayName }}</td>
                        <td>{{ invoice.periodName }}</td>
                        <td class="text-end">{{ formatMoney(invoice.advance ? invoice.cost - invoice.advance : invoice.cost) }}</td>
                        <td class="text-end">{{ formatMoney(invoice.paid) }}</td>
                        <td class="text-end"
                            :class="[invoice.advance ? 'text-success' : '', invoice.delta ? 'text-danger' : '']">
                            {{ invoice.advance ? formatMoney(-invoice.advance) : formatMoney(invoice.delta) }}
                        </td>
                        <td>{{ invoice.updated }}</td>
                    </tr>
                    </tbody>
                </table>

                <div v-else-if="!loading && invoices.length === 0" class="text-center text-muted py-3">
                    Нет счетов для отображения
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
}                                  from 'vue';
import LoadingSpinner              from '@common/LoadingSpinner.vue';
import { useResponseError }        from '@composables/useResponseError';
import { adminAccountInvoiceList } from '../../../../routes-functions.js';
import { useFormat }               from '@composables/useFormat.js';

const props = defineProps({
    account: {
        type    : Object,
        required: true,
    },
});

const { parseResponseErrors } = useResponseError();
const { formatMoney }         = useFormat();

// Состояния
const loading  = ref(false);
const invoices = ref([]);

// Загрузка списка счетов
const loadInvoices = async () => {
    if (!props.account?.id) {
        return;
    }

    loading.value = true;
    try {
        const response = await adminAccountInvoiceList(props.account.id);
        invoices.value = response.data.invoices || [];
    }
    catch (error) {
        parseResponseErrors(error);
        invoices.value = [];
    }
    finally {
        loading.value = false;
    }
};

// Следим за изменением account.id
watch(() => props.account?.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        loadInvoices();
    }
}, { immediate: true });

onMounted(() => {
    if (props.account?.id) {
        loadInvoices();
    }
});
</script>