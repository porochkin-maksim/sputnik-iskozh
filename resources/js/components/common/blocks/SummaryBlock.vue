<template>
    <div v-if="summary" class="table-responsive">
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
                       v-if="!summaryIncome"
                       @click.prevent="showDetailsIncome">Подробнее</a>
                    <a href="#"
                       v-else
                       @click.prevent="summaryIncome = null">Скрыть</a>
                </th>
                <td class="text-end">{{ formatMoney(summary.incomeCost) }}</td>
                <td class="text-end">{{ formatMoney(summary.incomePayed) }}</td>
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
                    <td class="text-end">{{ formatMoney(parseFloat(item.payed)) }}</td>
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
                       v-if="!summaryOutcome"
                       @click.prevent="showDetailsOutcome">Подробнее</a>
                    <a href="#"
                       v-else
                       @click.prevent="summaryOutcome = null">Скрыть</a>
                </th>
                <td class="text-end">{{ formatMoney(summary.outcomeCost) }}</td>
                <td class="text-end">{{ formatMoney(summary.outcomePayed) }}</td>
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
                    <td class="text-end">{{ formatMoney(parseFloat(item.payed)) }}</td>
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
                <td class="text-end">{{ formatMoney(summary.deltaPayed) }}</td>
                <td class="text-end">{{ formatMoney(summary.delta) }}</td>
                <template v-if="showInvoice">
                    <td>Расходных</td>
                    <td>{{ summary.outcomeCount }}</td>
                </template>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import Url                  from '@utils/Url.js';

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

const buildParams = () => {
    return {
        type      : props.type,
        period_id : props.periodId,
        account_id: props.accountId,
        search    : props.accountSearch,
    };
};

const summaryAction = () => {
    summaryIncome.value  = null;
    summaryOutcome.value = null;

    Url.RouteFunctions.commonSummary(buildParams())
        .then(response => {
            summary.value = response.data;
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};

const showDetailsIncome = () => {
    summaryIncome.value = [];
    Url.RouteFunctions.commonSummaryDetailing('income')
        .then(response => {
            summaryIncome.value = response.data;
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};

const showDetailsOutcome = () => {
    summaryOutcome.value = [];

    Url.RouteFunctions.commonSummaryDetailing('outcome')
        .then(response => {
            summaryOutcome.value = response.data;
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};

// Следим за изменениями параметров
watch(() => props.type, summaryAction);
watch(() => props.periodId, summaryAction);
watch(() => props.accountId, summaryAction);
watch(() => props.accountSearch, summaryAction);

// Загружаем данные при монтировании
onMounted(summaryAction);
</script>