<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="md"
            color="primary"
            text="Загрузка сводки..."
            wrapper-class="py-4"
        />

        <div v-else-if="summary" class="table-responsive">
            <table class="table table-sm table-bordered table-striped-columns text-center">
                <thead>
                <tr class="table-info">
                    <th></th>
                    <th class="text-center">План</th>
                    <th class="text-center">Оплачено</th>
                    <th class="text-center">Долг</th>
                    <template v-if="showInvoice">
                        <th colspan="2">Всего счетов {{ summary.totalCount }}</th>
                    </template>
                </tr>
                </thead>
                <tbody>
                <!-- Доход -->
                <tr class="table-secondary">
                    <th class="d-flex flex-column flex-sm-row flex-wrap justify-content-between">
                        <span>Доход</span>
                        <a href="#"
                           v-if="!summaryIncome && !detailsLoading"
                           @click.prevent="showDetailsIncome">Подробнее</a>
                        <a href="#"
                           v-else-if="summaryIncome && !detailsLoading"
                           @click.prevent="summaryIncome = null">Скрыть</a>
                        <span v-else class="text-muted small">
                            <i class="fa fa-spinner fa-spin me-1"></i>Загрузка...
                        </span>
                    </th>
                    <td class="text-end">{{ formatMoney(summary.incomeCost) }}</td>
                    <td class="text-end">{{ formatMoney(summary.incomePaid) }}</td>
                    <td class="text-end">{{ formatMoney(summary.deltaIncome) }}</td>
                    <template v-if="showInvoice">
                        <td>Регулярных</td>
                        <td>{{ summary.regularCount }}</td>
                    </template>
                </tr>
                <template v-if="summaryIncome">
                    <tr v-for="item in summaryIncome" :key="item.id" class="">
                        <td class="text-start">{{ item.service }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.cost)) }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.paid)) }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.delta)) }}</td>
                        <template v-if="showInvoice">
                            <td></td>
                            <td></td>
                        </template>
                    </tr>
                </template>

                <!-- Расход -->
                <tr class="table-secondary">
                    <th class="d-flex flex-column flex-sm-row flex-wrap justify-content-between">
                        <span>Расход</span>
                        <a href="#"
                           v-if="!summaryOutcome && !detailsLoading"
                           @click.prevent="showDetailsOutcome">Подробнее</a>
                        <a href="#"
                           v-else-if="summaryOutcome && !detailsLoading"
                           @click.prevent="summaryOutcome = null">Скрыть</a>
                        <span v-else class="text-muted small">
                            <i class="fa fa-spinner fa-spin me-1"></i>Загрузка...
                        </span>
                    </th>
                    <td class="text-end">{{ formatMoney(summary.outcomeCost) }}</td>
                    <td class="text-end">{{ formatMoney(summary.outcomePaid) }}</td>
                    <td class="text-end">{{ formatMoney(summary.deltaOutcome) }}</td>
                    <template v-if="showInvoice">
                        <td>Доходных</td>
                        <td>{{ summary.incomeCount }}</td>
                    </template>
                </tr>
                <template v-if="summaryOutcome">
                    <tr v-for="item in summaryOutcome" :key="item.id" class="">
                        <td class="text-start">{{ item.service }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.cost)) }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.paid)) }}</td>
                        <td class="text-end">{{ formatMoney(parseFloat(item.delta)) }}</td>
                        <template v-if="showInvoice">
                            <td></td>
                            <td></td>
                        </template>
                    </tr>
                </template>

                <!-- Итого -->
                <tr class="table-info">
                    <th class="text-end">Итого:</th>
                    <td class="text-end">{{ formatMoney(summary.deltaCost) }}</td>
                    <td class="text-end">{{ formatMoney(summary.deltaPaid) }}</td>
                    <td class="text-end">{{ formatMoney(summary.delta) }}</td>
                    <template v-if="showInvoice">
                        <td>Расходных</td>
                        <td>{{ summary.outcomeCount }}</td>
                    </template>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Сообщение при отсутствии данных -->
        <div v-else-if="!isLoading && !summary" class="alert alert-info text-center my-3">
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            Нет данных для отображения
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiCommonSummary,
    ApiCommonSummaryDetailing,
}                           from '@api';

defineOptions({
    name: 'SummaryBlock',
});

const props = defineProps({
    type         : {
        type   : Number,
        default: null,
    },
    periodId     : {
        type   : Number,
        default: null,
    },
    accountId    : {
        type   : Number,
        default: null,
    },
    accountSearch: {
        type   : String,
        default: null,
    },
    showInvoice  : {
        type   : Boolean,
        default: true,
    },
});

const { parseResponseErrors } = useResponseError();
const { formatMoney }         = useFormat();

const summary        = ref(null);
const summaryIncome  = ref(null);
const summaryOutcome = ref(null);
const isLoading      = ref(false);
const detailsLoading = ref(false);

const buildParams = () => ({
    type      : props.type,
    period_id : props.periodId,
    account_id: props.accountId,
    search    : props.accountSearch,
});

const summaryAction = async () => {
    isLoading.value      = true;
    summaryIncome.value  = null;
    summaryOutcome.value = null;

    try {
        const response = await ApiCommonSummary(buildParams());
        summary.value  = response.data;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

const showDetailsIncome = async () => {
    if (detailsLoading.value) {
        return;
    }

    detailsLoading.value = true;
    summaryIncome.value  = [];
    try {
        const response      = await ApiCommonSummaryDetailing('income', buildParams());
        summaryIncome.value = response.data;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        detailsLoading.value = false;
    }
};

const showDetailsOutcome = async () => {
    if (detailsLoading.value) {
        return;
    }

    detailsLoading.value = true;
    summaryOutcome.value = [];
    try {
        const response       = await ApiCommonSummaryDetailing('outcome', buildParams());
        summaryOutcome.value = response.data;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        detailsLoading.value = false;
    }
};

// Следим за изменениями параметров
watch(() => props.type, summaryAction);
watch(() => props.periodId, summaryAction);
watch(() => props.accountId, summaryAction);
watch(() => props.accountSearch, summaryAction);

// Загружаем данные при монтировании
onMounted(summaryAction);
</script>