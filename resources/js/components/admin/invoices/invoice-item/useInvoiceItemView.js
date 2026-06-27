import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminInvoiceGet,
    ApiAdminInvoiceDelete,
    ApiAdminInvoiceRecalc,
    ApiAdminInvoiceSave,
    ApiAdminPaymentManageAccountBalance,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';

export function useInvoiceItemView (props) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { formatMoney }                               = useFormat();
    const { has }                                       = usePermissions();

    const localInvoice    = ref({});
    const actions         = ref({});
    const reload          = ref(false);
    const claimsCount     = ref(0);
    const paymentsCount   = ref(0);
    const isLoading       = ref(true);
    const isRecalculating   = ref(false);
    const balance           = ref(null);
    const showRoundingModal = ref(false);
    const roundingValue     = ref(0);

    const canView        = computed(() => has('invoices', 'view'));
    const canEdit        = computed(() => has('invoices', 'edit') && actions.value.edit);
    const canDrop        = computed(() => has('invoices', 'drop'));
    const canAccountView = computed(() => has('accounts', 'view'));

    const statusAlertClass = computed(() => {
        if (!localInvoice.value.isPaid && localInvoice.value.cost === 0) {
            return 'alert-secondary';
        }
        return localInvoice.value.isPaid ? 'alert-success' : 'alert-warning';
    });

    const deltaClass = computed(() => {
        return localInvoice.value.delta > 0 ? 'text-danger fw-bold' : 'text-success';
    });

    const canDelete = computed(() => {
        return canDrop.value && actions.value.drop && claimsCount.value === 0 && paymentsCount.value === 0;
    });

    const loadInvoice = async () => {
        isLoading.value = true;
        try {
            const response     = await ApiAdminInvoiceGet(props.invoice.id);
            localInvoice.value = response.data;
            actions.value      = response.data.actions || {};
            await loadBalance();
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            isLoading.value = false;
        }
    };

    const loadBalance = async () => {
        const accountId = localInvoice.value.accountId;
        if ( ! accountId) {
            balance.value = null;
            return;
        }
        try {
            const response = await ApiAdminPaymentManageAccountBalance(accountId);
            balance.value = response.data?.balance ?? null;
        }
        catch {
            balance.value = null;
        }
    };

    const dropAction = async () => {
        if (!confirm('Удалить счёт?')) {
            return;
        }

        try {
            const response = await ApiAdminInvoiceDelete(props.invoice.id);
            if (response.data) {
                showInfo('Счёт удалён');
                setTimeout(() => {
                    window.location.href = routeUri('adminInvoiceIndex');
                }, 1000);
            }
            else {
                showDanger('Счёт не удалён');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const openRoundingEdit = () => {
        roundingValue.value = localInvoice.value.detailCost?.rounding ?? 0;
        showRoundingModal.value = true;
    };

    const updateRoundingAction = async () => {
        try {
            const inv = localInvoice.value;
            await ApiAdminInvoiceSave({
                id        : inv.id,
                period_id : inv.periodId,
                account_id: inv.accountId,
                type      : inv.type,
                name      : inv.name,
                rounding  : roundingValue.value,
            });
            showRoundingModal.value = false;
            await loadInvoice();
            showInfo('Округление сохранено');
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const recalcAction = async () => {
        isRecalculating.value = true;
        try {
            const response = await ApiAdminInvoiceRecalc(props.invoice.id);
            if (response.data) {
                await loadInvoice();
                showInfo('Счёт пересчитан');
            }
            else {
                showDanger('Не удалось пересчитать счёт');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            isRecalculating.value = false;
        }
    };

    watch(reload, (value) => {
        if (value) {
            loadInvoice();
            setTimeout(() => {
                reload.value = false;
            }, 100);
        }
    });

    onMounted(() => {
        localInvoice.value = props.invoice;
        actions.value      = props.invoice.actions || {};
        loadInvoice();
    });

    return {
        actions,
        balance,
        canAccountView,
        canDelete,
        canEdit,
        canView,
        claimsCount,
        deltaClass,
        dropAction,
        formatMoney,
        isLoading,
        isRecalculating,
        localInvoice,
        loadInvoice,
        paymentsCount,
        recalcAction,
        reload,
        roundingValue,
        showRoundingModal,
        openRoundingEdit,
        updateRoundingAction,
        statusAlertClass,
    };
}
