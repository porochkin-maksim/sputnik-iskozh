import {
    computed,
    ref,
    watch,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminNewPaymentView,
    ApiAdminNewPaymentSave,
    ApiAdminNewPaymentGetInvoices,
}                           from '@api';

export function usePaymentsBlock (props, emit) {
    const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

    const reloadList      = ref(false);
    const payment         = ref(null);
    const selectedId      = ref(null);
    const accounts        = ref([]);
    const invoices        = ref([]);
    const periods         = ref([]);
    const periodId        = ref(null);
    const isLoading       = ref(false);
    const invoicesLoading = ref(false);
    const saving          = ref(false);
    const showDialog      = ref(false);
    const hideDialog      = ref(false);

    const getAction = async () => {
        periodId.value = null;
        accounts.value = [];
        invoices.value = [];
        periods.value  = [];

        try {
            const response        = await ApiAdminNewPaymentView(selectedId.value);
            payment.value         = response.data.payment;
            periodId.value        = response.data.payment.invoice?.periodId;
            accounts.value        = response.data.accounts || [];
            periods.value         = response.data.periods || [];
            payment.value.cost    = parseFloat(payment.value.cost).toFixed(2);
            payment.value.comment = payment.value.comment ? String(payment.value.comment) : null;
            showDialog.value      = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const saveAction = async () => {
        saving.value = true;

        const data = {
            id        : payment.value.id,
            name      : payment.value.name || '',
            cost      : parseFloat(payment.value.cost),
            comment   : payment.value.comment || '',
            account_id: payment.value.accountId,
            invoice_id: payment.value.invoiceId,
        };

        try {
            await ApiAdminNewPaymentSave({}, data);
            showInfo('Платёж привязан');
            payment.value = null;
            onSaved();
        }
        catch (error) {
            const message = error?.response?.data?.message || 'Не получилось привязать платёж';
            showDanger(message);
            parseResponseErrors(error);
        }
        finally {
            saving.value     = false;
            selectedId.value = null;
        }
    };

    const closeAction = () => {
        payment.value    = null;
        selectedId.value = null;
        periodId.value   = null;
        showDialog.value = false;
    };

    const onSaved = () => {
        closeAction();
        reloadList.value = true;
        emit('update:reload', true);
    };

    const getInvoices = async () => {
        if (!periodId.value || !payment.value?.accountId) {
            if (payment.value) {
                payment.value.invoiceId = null;
            }
            invoices.value = [];
            return;
        }

        invoicesLoading.value = true;
        if (payment.value) {
            payment.value.invoiceId = null;
        }
        invoices.value = [];

        try {
            const response = await ApiAdminNewPaymentGetInvoices(
                payment.value.accountId,
                periodId.value,
            );
            invoices.value = response.data.invoices || [];
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            invoicesLoading.value = false;
        }
    };

    const onFileUpdated = () => {
        getAction();
    };

    const canSave = computed(() => {
        return payment.value &&
            payment.value.cost > 0 &&
            payment.value.invoiceId;
    });

    watch(() => props.reload, (value) => {
        if (value) {
            reloadList.value = true;
        }
    });

    watch(selectedId, (value) => {
        if (value) {
            getAction();
        }
        else {
            payment.value = null;
        }
    });

    watch(hideDialog, () => {
        closeAction();
    });

    watch(reloadList, (value) => {
        emit('update:reload', value);
    });

    return {
        errors,
        clearError,
        reloadList,
        payment,
        selectedId,
        accounts,
        invoices,
        periods,
        periodId,
        isLoading,
        invoicesLoading,
        saving,
        showDialog,
        hideDialog,
        getInvoices,
        saveAction,
        closeAction,
        onFileUpdated,
        canSave,
    };
}
