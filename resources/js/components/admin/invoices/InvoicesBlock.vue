<template>
    <!-- Индикатор загрузки -->
    <loading-spinner
        v-if="!loaded"
        size="lg"
        color="primary"
        text="Загрузка счетов..."
        wrapper-class="my-5"
    />

    <template v-else>
        <!-- Нет периодов -->
        <div v-if="!periods || !periods.length">
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="fa fa-warning fa-lg" aria-hidden="true"></i>
                <div>
                    <p class="mb-1">Не найдено ни одного периода</p>
                    <a :href="Url.Routes.adminPeriodIndex.uri" class="alert-link">
                        Создайте период
                    </a>
                </div>
            </div>
        </div>

        <!-- Основной контент -->
        <template v-else>
            <!-- Верхняя панель с кнопками и пагинацией -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <div class="btn-group" role="group">
                        <button class="btn btn-success"
                                v-if="actions.edit"
                                @click="makeAction">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Добавить счёт
                        </button>
                        <button class="btn btn-success"
                                v-if="actions.edit && periodId"
                                @click="makeRegularAction">
                            <i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
                            Выставить регулярные
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <pagination :total="total"
                                :per-page="perPage"
                                :page="Math.ceil(skip / perPage) + 1"
                                :prop-classes="'pagination-sm mb-0'"
                                @update="onPaginationUpdate" />

                    <simple-select v-model="perPage"
                                   class="d-inline-block form-select-sm w-auto"
                                   :options="[15, 25, 50, 100]"
                                   @change="listAction"
                                   aria-label="Элементов на странице" />

                    <span class="badge bg-secondary">
                        Всего: {{ total }}
                    </span>

                    <history-btn class="btn-link underline-none p-0"
                                 :url="historyUrl"
                                 aria-label="История изменений" />
                </div>
            </div>

            <!-- Панель фильтров -->
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Период -->
                    <simple-select v-if="computedPeriods?.length"
                                   v-model="periodId"
                                   class="form-select-sm w-auto"
                                   :options="computedPeriods"
                                   @change="listAction"
                                   aria-label="Фильтр по периоду" />

                    <!-- Тип -->
                    <simple-select v-if="computedTypes?.length"
                                   v-model="type"
                                   class="form-select-sm w-auto"
                                   :options="computedTypes"
                                   @change="listAction"
                                   aria-label="Фильтр по типу" />

                    <!-- Статус оплаты -->
                    <simple-select v-if="computedPaidStatus?.length"
                                   v-model="paidStatus"
                                   class="form-select-sm w-auto"
                                   :options="computedPaidStatus"
                                   @change="listAction"
                                   aria-label="Фильтр по статусу оплаты" />

                    <!-- Поиск по участку -->
                    <div v-if="computedAccounts?.length" class="input-group input-group-sm"
                         style="min-width: 80px; max-width: 150px;">
                        <button class="btn btn-light border px-2"
                                @click="searchAction"
                                :disabled="!searchAccount && searchAccount !== ''"
                                type="button"
                                aria-label="Поиск">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <input class="form-control"
                               v-model="searchAccount"
                               placeholder="Участок"
                               @keyup.enter="searchAction"
                               ref="searchInput"
                               aria-label="Поиск по участку"
                               style="min-width: 50px;">
                        <button class="btn btn-light border px-2"
                                v-if="searchAccount"
                                @click="clearSearchAction"
                                type="button"
                                aria-label="Очистить поиск">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-success"
                                v-if="actions.edit && periodId"
                                @click="importAction">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline ms-1">Импорт</span>
                        </button>
                        <button class="btn btn-success"
                                @click="exportAction"
                                aria-label="Экспорт в Excel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            <span class="d-none d-sm-inline ms-1">Экспорт</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Сводка -->
            <summary-block :account-id="parseInt(accountId)"
                           :account-search="searchAccount"
                           :type="parseInt(type)"
                           :period-id="parseInt(periodId)"
                           class="mb-3" />

            <!-- Список счетов -->
            <invoices-list :invoices="invoices"
                           :sort-field="sortField"
                           :sort-order="sortOrder"
                           @sort="onSort" />

            <!-- Редактирование счёта -->
            <invoice-item-edit v-if="invoice && actions.edit"
                               :model-value="invoice"
                               :accounts="accounts"
                               :periods="activePeriods"
                               :types="activeTypes"
                               @updated="listAction" />
        </template>
    </template>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    watch,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import InvoiceItemEdit      from './InvoiceItemEdit.vue';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import Pagination           from '../../common/pagination/Pagination.vue';
import SimpleSelect         from '../../common/form/SimpleSelect.vue';
import InvoicesList         from './InvoicesList.vue';
import SummaryBlock         from '../../common/blocks/SummaryBlock.vue';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import {
    ApiAdminInvoiceCreate,
    ApiAdminInvoiceList,
    ApiAdminInvoiceGetAccountsCountWithoutRegular,
    ApiAdminInvoiceCreateRegularInvoices,
}                           from '@api';
import Url                  from '@utils/Url.js';

defineOptions({
    name: 'InvoicesBlock',
});

const { parseResponseErrors, showInfo, showSuccess } = useResponseError();

const invoice        = ref(null);
const invoices       = ref([]);
const accounts       = ref([]);
const periods        = ref([]);
const activePeriods  = ref([]);
const types          = ref([]);
const activeTypes    = ref([]);
const historyUrl     = ref(null);
const loaded         = ref(false);
const total          = ref(null);
const perPage        = ref(25);
const skip           = ref(0);
const routeState     = ref(0);
const type           = ref(0);
const periodId       = ref(null);
const paidStatus     = ref('all');
const accountId      = ref(0);
const searchAccount  = ref(null);
const actions        = ref({});
const sortField      = ref('id');
const sortOrder      = ref('desc');
const searchProgress = ref(null);
const searchInput    = ref(null);

// Инициализация из URL параметров
const initFromUrl = () => {
    const urlParams     = new URLSearchParams(window.location.search);
    perPage.value       = parseInt(urlParams.get('limit') || 25);
    skip.value          = parseInt(urlParams.get('skip') || 0);
    type.value          = parseInt(urlParams.get('type') || 0);
    periodId.value      = parseInt(urlParams.get('period') || 0);
    paidStatus.value    = urlParams.get('status') || 'all';
    sortField.value     = urlParams.get('sort_field') || 'id';
    sortOrder.value     = urlParams.get('sort_order') || 'desc';
    searchAccount.value = urlParams.get('search') || null;
};

const computedTypes = computed(() => [
    { value: 0, label: 'Все типы' },
    ...types.value,
]);

const computedPeriods = computed(() => [
    { value: 0, label: 'Все периоды' },
    ...periods.value,
]);

const computedAccounts = computed(() => [
    { value: 0, label: 'Все участки' },
    ...accounts.value,
]);

const computedPaidStatus = computed(() => [
    { value: 'all', label: 'Все статусы' },
    { value: 'paid', label: 'Оплаченные' },
    { value: 'unpaid', label: 'Неоплаченные' },
    { value: 'partial', label: 'Частично оплаченные' },
]);

const makeRegularAction = async () => {
    try {
        const countResponse = await ApiAdminInvoiceGetAccountsCountWithoutRegular(periodId.value);
        const count         = countResponse.data;

        if (count === 0) {
            alert('Нет ни одного участка для выставления регулярного счёта в периоде');
            return;
        }

        if (!confirm(`Выставить регулярные счета всем участкам в периоде, у которых ещё нет таких счетов? (${count}шт)`)) {
            return;
        }

        const response = await ApiAdminInvoiceCreateRegularInvoices(periodId.value);
        if (response.data) {
            await listAction();
            showSuccess('Процесс выставления счетов запущен');
        }
        else {
            showInfo('Процесс выставления счетов уже запущен');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const makeAction = async () => {
    invoice.value = null;
    try {
        const response   = await ApiAdminInvoiceCreate();
        const newInvoice = response.data;

        if (periodId.value) {
            newInvoice.periodId = periodId.value;
        }
        if (type.value) {
            newInvoice.type = type.value;
        }

        invoice.value = newInvoice;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const listAction = async () => {
    const uri = Url.Generator.makeUri(Url.Routes.adminInvoiceIndex, {}, {
        limit     : perPage.value,
        skip      : skip.value,
        type      : type.value,
        period    : periodId.value,
        account   : accountId.value,
        search    : searchAccount.value,
        status    : paidStatus.value,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
    });

    window.history.pushState({ state: routeState.value++ }, '', uri);

    try {
        const response = await ApiAdminInvoiceList({
            limit      : perPage.value,
            skip       : skip.value,
            type       : type.value,
            period_id  : periodId.value,
            account_id : accountId.value,
            account    : searchAccount.value,
            paid_status: paidStatus.value,
            sort_field : sortField.value,
            sort_order : sortOrder.value,
        });

        actions.value       = response.data.actions;
        invoices.value      = response.data.invoices;
        total.value         = response.data.total;
        types.value         = response.data.types;
        activeTypes.value   = response.data.activeTypes;
        periods.value       = response.data.periods;
        activePeriods.value = response.data.activePeriods;
        accounts.value      = response.data.accounts;
        historyUrl.value    = response.data.historyUrl;

        if ((periodId.value === null || periodId.value === undefined) && periods.value.length) {
            periodId.value = periods.value[0].value;
            await listAction();
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loaded.value = true;
    }
};

const searchAction = () => {
    clearTimeout(searchProgress.value);
    searchProgress.value = setTimeout(() => {
        skip.value = 0;
        listAction();
    }, 300);
};

const clearSearchAction = () => {
    searchAccount.value = null;
    searchAction();
    searchInput.value?.focus();
};

const exportAction = () => {
    const url = Url.Generator.makeUri(Url.Routes.adminInvoiceExport, {}, {
        type       : type.value,
        period_id  : periodId.value,
        account_id : accountId.value,
        account    : searchAccount.value,
        paid_status: paidStatus.value,
        sort_field : sortField.value,
        sort_order : sortOrder.value,
    });
    window.open(url, '_blank');
};

const importAction = () => {
    const url = Url.Generator.makeUri(Url.Routes.adminInvoiceImportPaymentsIndex, {
        periodId: periodId.value,
    });
    window.open(url, '_blank');
};

const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
    listAction();
};

const onSort = ({ field, order }) => {
    sortField.value = field;
    sortOrder.value = order;
    listAction();
};

onMounted(() => {
    initFromUrl();
    listAction();
});

// Следим за изменениями accountId (может меняться из summary)
watch(accountId, () => {
    listAction();
});
</script>