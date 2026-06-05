<template>
    <div class="page-card p-0">
        <loading-spinner
            v-if="isLoading"
            size="md"
            color="primary"
            text="Загрузка сводки..."
            wrapper-class="py-5"
        />

        <div v-else-if="summary" class="profile-summary__scroll">
            <table class="profile-summary">
                <thead class="profile-summary__head">
                <tr class="profile-summary__header-row">
                    <th class="profile-summary__header-cell profile-summary__header-cell--label"></th>
                    <th class="profile-summary__header-cell profile-summary__header-cell--number">План</th>
                    <th class="profile-summary__header-cell profile-summary__header-cell--number">Оплачено</th>
                    <th class="profile-summary__header-cell profile-summary__header-cell--number">Долг</th>
                    <template v-if="showInvoice">
                        <th class="profile-summary__header-cell profile-summary__header-cell--label"
                            colspan="2">Всего счетов {{ summary.totalCount }}
                        </th>
                    </template>
                </tr>
                </thead>
                <tbody>
                <tr class="profile-summary__row profile-summary__row--section">
                    <td class="profile-summary__cell profile-summary__cell--label profile-summary__cell--toggle"
                        @click="summaryIncome ? summaryIncome = null : showDetailsIncome()">
                        <span class="profile-summary__toggle-icon">
                            <i class="fa" :class="summaryIncome ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </span>
                        <span>Доход</span>
                    </td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.incomeCost) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.incomePaid) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.deltaIncome) }}</td>
                    <template v-if="showInvoice">
                        <td class="profile-summary__cell profile-summary__cell--number">Регулярных</td>
                        <td class="profile-summary__cell profile-summary__cell--number">{{ summary.regularCount }}</td>
                    </template>
                </tr>
                <template v-if="summaryIncome">
                    <tr v-for="item in summaryIncome" :key="item.id"
                        class="profile-summary__row profile-summary__row--detail">
                        <td class="profile-summary__cell profile-summary__cell--label profile-summary__cell--sub">{{ item.service }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.cost)) }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.paid)) }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.delta)) }}</td>
                        <template v-if="showInvoice">
                            <td class="profile-summary__cell--sub"></td>
                            <td class="profile-summary__cell--sub"></td>
                        </template>
                    </tr>
                </template>

                <tr class="profile-summary__row profile-summary__row--section">
                    <td class="profile-summary__cell profile-summary__cell--label profile-summary__cell--toggle"
                        @click="summaryOutcome ? summaryOutcome = null : showDetailsOutcome()">
                        <span class="profile-summary__toggle-icon" :style="summary.deltaOutcome ? '' : 'opacity:0;'">
                            <i class="fa" :class="summaryOutcome ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </span>
                        <span>Расход</span>
                    </td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.outcomeCost) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.outcomePaid) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.deltaOutcome) }}</td>
                    <template v-if="showInvoice">
                        <td class="profile-summary__cell profile-summary__cell--number">Доходных</td>
                        <td class="profile-summary__cell profile-summary__cell--number">{{ summary.incomeCount }}</td>
                    </template>
                </tr>
                <template v-if="summaryOutcome">
                    <tr v-for="item in summaryOutcome" :key="item.id"
                        class="profile-summary__row profile-summary__row--detail">
                        <td class="profile-summary__cell profile-summary__cell--label profile-summary__cell--sub">{{ item.service }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.cost)) }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.paid)) }}</td>
                        <td class="profile-summary__cell profile-summary__cell--number profile-summary__cell--sub">{{ formatMoney(parseFloat(item.delta)) }}</td>
                        <template v-if="showInvoice">
                            <td class="profile-summary__cell--sub"></td>
                            <td class="profile-summary__cell--sub"></td>
                        </template>
                    </tr>
                </template>

                <tr class="profile-summary__row profile-summary__row--total">
                    <td class="profile-summary__cell profile-summary__cell--label">Итого:</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.deltaCost) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.deltaPaid) }}</td>
                    <td class="profile-summary__cell profile-summary__cell--number">{{ formatMoney(summary.delta) }}</td>
                    <template v-if="showInvoice">
                        <td class="profile-summary__cell profile-summary__cell--number">Расходных</td>
                        <td class="profile-summary__cell profile-summary__cell--number">{{ summary.outcomeCount }}</td>
                    </template>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="!isLoading && !summary" class="profile-summary__empty">
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
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiCommonSummary,
    ApiCommonSummaryDetailing,
}                           from '@api';

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
    if (detailsLoading.value || !summary.deltaOutcome) {
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

watch(() => props.type, summaryAction);
watch(() => props.periodId, summaryAction);
watch(() => props.accountId, summaryAction);
watch(() => props.accountSearch, summaryAction);

onMounted(summaryAction);
</script>