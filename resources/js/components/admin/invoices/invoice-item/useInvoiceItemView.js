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
    const isRecalculating = ref(false);

    const canView        = computed(() => has('invoices', 'view'));
    const canEdit        = computed(() => has('invoices', 'edit'));
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
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            isLoading.value = false;
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
        statusAlertClass,
    };
}
