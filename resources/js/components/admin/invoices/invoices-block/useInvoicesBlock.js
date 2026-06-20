import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminInvoiceCreate,
    ApiAdminInvoiceCreateRegularInvoices,
    ApiAdminInvoiceGetAccountsCountWithoutRegular,
    ApiAdminInvoiceList,
    ApiAdminInvoiceRecalcPeriod,
    ApiAdminInvoiceResetPaymentsPeriod,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';

export function useInvoicesBlock () {
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
    const total          = ref(0);
    const perPage        = ref(25);
    const skip           = ref(0);
    const routeState     = ref(0);
    const type           = ref(0);
    const periodId       = ref(null);
    const paidStatus     = ref('all');
    const accountId      = ref(0);
    const searchAccount  = ref(null);
    const sortField      = ref('id');
    const sortOrder      = ref('desc');
    const searchProgress = ref(null);
    const periodIndexUrl = routeUri('adminPeriodIndex');

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

    const listAction = async () => {
        const uri = routeUri('adminInvoiceIndex', {}, {
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
    };

    const exportAction = () => {
        const url = routeUri('adminInvoiceExport', {}, {
            type       : type.value,
            period     : periodId.value,
            account_id : accountId.value,
            account    : searchAccount.value,
            paid_status: paidStatus.value,
            sort_field : sortField.value,
            sort_order : sortOrder.value,
        });
        window.open(url, '_blank');
    };

    const importAction = () => {
        const url = routeUri('adminInvoiceImportPaymentsIndex', {
            periodId: periodId.value,
        });
        window.open(url, '_blank');
    };

    const recalcAction = async () => {
        if (!periodId.value) {
            return;
        }
        if (!confirm('Пересчитать все счета периода? Это может занять время.')) {
            return;
        }
        try {
            const response = await ApiAdminInvoiceRecalcPeriod(periodId.value, {}, {});
            showSuccess(`Пересчёт запущен: ${response.data.sent} отправлено, ${response.data.blocked} уже в очереди`);
        }
        catch (err) {
            parseResponseErrors(err);
        }
    };

    const resetPaymentsAction = async () => {
        if (!periodId.value) {
            return;
        }
        if (!confirm('Сбросить все оплаты за период?')) {
            return;
        }
        try {
            await ApiAdminInvoiceResetPaymentsPeriod(periodId.value);
            showSuccess('Оплаты сброшены');
            await listAction();
        }
        catch (err) {
            parseResponseErrors(err);
        }
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

    watch(accountId, () => {
        listAction();
    });

    onMounted(() => {
        initFromUrl();
        listAction();
    });

    return {
        activePeriods,
        activeTypes,
        accountId,
        accounts,
        clearSearchAction,
        computedAccounts,
        computedPaidStatus,
        computedPeriods,
        computedTypes,
        currentPage: computed(() => Math.ceil(skip.value / perPage.value) + 1),
        exportAction,
        historyUrl,
        importAction,
        recalcAction,
        resetPaymentsAction,
        invoice,
        invoices,
        listAction,
        loaded,
        makeAction,
        makeRegularAction,
        onPaginationUpdate,
        onSort,
        paidStatus,
        perPage,
        periodId,
        periodIndexUrl,
        periods,
        searchAccount,
        searchAction,
        sortField,
        sortOrder,
        skip,
        total,
        type,
    };
}
