import {
    ref,
    computed,
    watch,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import {
    ApiAdminPaymentManageCreate,
    ApiAdminPaymentManageView,
    ApiAdminPaymentManageSave,
    ApiAdminPaymentManageDelete,
}                           from '@api';

export function usePaymentsManage (props, emit) {
    const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { formatMoney }                                                   = useFormat();

    const paymentsCount = ref(0);
    const reloadList    = ref(false);
    const payment       = ref(null);
    const selectedId    = ref(null);
    const files         = ref([]);
    const loading       = ref(false);
    const showDialog    = ref(false);
    const hideDialog    = ref(false);
    const fileElem      = ref(null);

    const canSave         = computed(() => payment.value && payment.value.cost >= 0);
    const filesSize       = computed(() => {
        const total = files.value.reduce((acc, file) => acc + file.size, 0);
        return (total / (1024 * 1024)).toFixed(2);
    });
    const fileSizeExceed  = computed(() => parseFloat(filesSize.value) > 20);
    const fileCountExceed = computed(() => files.value.length > 4);

    const listParams = computed(() => {
        const params = {};
        if (props.invoiceId) {
            params.invoice_id = props.invoiceId;
        }
        if (props.accountId) {
            params.account_id = props.accountId;
        }
        return params;
    });

    const init = () => {
        paymentsCount.value = props.count || 0;
    };

    const makeAction = async () => {
        try {
            const response         = await ApiAdminPaymentManageCreate(listParams.value);
            payment.value          = response.data.payment;
            payment.value.accounts = response.data.accounts || [];
            payment.value.periods  = response.data.periods || [];
            if (!payment.value.accountId && props.accountId) {
                payment.value.accountId = props.accountId;
            }
            showDialog.value = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const transactions = ref([]);

    const getAction = async () => {
        try {
            const response         = await ApiAdminPaymentManageView(selectedId.value);
            payment.value          = response.data.payment;
            payment.value.accounts = response.data.accounts || [];
            payment.value.periods  = response.data.periods || [];
            payment.value.cost     = parseFloat(payment.value.cost).toFixed(2);
            payment.value.comment  = payment.value.comment ? String(payment.value.comment) : null;
            transactions.value     = response.data.transactions || [];
            showDialog.value       = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const saveAction = async () => {
        loading.value = true;

        const formData = new FormData();
        formData.append('id', payment.value.id || '');
        formData.append('name', payment.value.name || '');
        formData.append('cost', parseFloat(payment.value.cost));
        formData.append('comment', payment.value.comment || '');
        formData.append('account_id', payment.value.accountId || '');
        formData.append('invoice_id', payment.value.invoiceId || '');
        formData.append('paidAt', payment.value.paid || '');

        files.value.forEach((file, index) => {
            formData.append(`file${index}`, file);
        });

        try {
            await ApiAdminPaymentManageSave({}, formData);
            const message = payment.value.id ? 'Платёж обновлён' : 'Платёж создан';
            showInfo(message);
            payment.value = null;
            onSaved();
            showDialog.value = false;
        }
        catch (error) {
            const message = error?.response?.data?.message ||
                `Не удалось ${payment.value.id ? 'сохранить' : 'создать'} платёж`;
            showDanger(message);
            parseResponseErrors(error);
        }
        finally {
            loading.value    = false;
            selectedId.value = null;
            files.value      = [];
        }
    };

    const deleteAction = async (id) => {
        if (!confirm('Удалить платёж?')) {
            return;
        }

        try {
            const response = await ApiAdminPaymentManageDelete(id);
            if (response.data) {
                reloadList.value = true;
                showInfo('Платёж удалён');
            }
            else {
                showDanger('Платеж не удалён');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const closeAction = () => {
        payment.value    = null;
        selectedId.value = null;
        files.value      = [];
    };

    const onSaved = () => {
        reloadList.value = true;
        emit('update:reload', true);
    };

    const chooseFiles = () => {
        fileElem.value?.click();
    };

    const appendFiles = (event) => {
        const newFiles = Array.from(event.target.files);
        for (const file of newFiles) {
            if (!fileCountExceed.value) {
                files.value.push(file);
            }
        }
        event.target.value = '';
    };

    const removeFile = (index) => {
        files.value = files.value.filter((_, i) => i !== index);
    };

    const onUpdatedCount = (value) => {
        paymentsCount.value = value;
        emit('update:count', value);
    };

    const onFileUpdated = () => {
        if (selectedId.value) {
            getAction();
        }
    };

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

    watch(paymentsCount, (value) => {
        emit('update:count', value);
    });

    onMounted(init);

    return {
        canSave,
        transactions,
        clearError,
        closeAction,
        chooseFiles,
        deleteAction,
        fileCountExceed,
        fileElem,
        fileSizeExceed,
        files,
        filesSize,
        formatMoney,
        getAction,
        hideDialog,
        loading,
        makeAction,
        onFileUpdated,
        onUpdatedCount,
        payment,
        paymentsCount,
        reloadList,
        saveAction,
        selectedId,
        showDialog,
        listParams,
    };
}
