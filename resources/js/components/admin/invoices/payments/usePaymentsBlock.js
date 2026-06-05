import {
    ref,
    computed,
    watch,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import {
    ApiAdminPaymentAutoCreate,
    ApiAdminPaymentCreate,
    ApiAdminPaymentView,
    ApiAdminPaymentSave,
}                           from '@api';

export function usePaymentsBlock (props, emit) {
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
    const forcePaid     = ref(false);
    const fileElem      = ref(null);

    const canSave         = computed(() => payment.value && payment.value.cost >= 0);
    const filesSize       = computed(() => {
        const total = files.value.reduce((acc, file) => acc + file.size, 0);
        return (total / (1024 * 1024)).toFixed(2);
    });
    const fileSizeExceed  = computed(() => parseFloat(filesSize.value) > 20);
    const fileCountExceed = computed(() => files.value.length > 4);

    const init = () => {
        paymentsCount.value = props.count || 0;
    };

    const makePaid = async () => {
        if (!confirm('Создать платежи для каждой неоплаченной услуги?')) {
            return;
        }

        loading.value = true;
        try {
            await ApiAdminPaymentAutoCreate(props.invoice.id);
            forcePaid.value = true;
            showInfo('Счёт оплачен');
            onSaved();
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    const makeAction = async () => {
        try {
            const response   = await ApiAdminPaymentCreate(props.invoice.id);
            payment.value    = response.data.payment;
            showDialog.value = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const getAction = async () => {
        try {
            const response        = await ApiAdminPaymentView(props.invoice.id, selectedId.value);
            payment.value         = response.data.payment;
            payment.value.cost    = parseFloat(payment.value.cost).toFixed(2);
            payment.value.comment = payment.value.comment ? String(payment.value.comment) : null;
            showDialog.value      = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const saveAction = async () => {
        loading.value = true;

        const formData = new FormData();
        formData.append('id', payment.value.id);
        formData.append('cost', parseFloat(payment.value.cost));
        formData.append('name', payment.value.name || '');
        formData.append('comment', payment.value.comment ? String(payment.value.comment) : '');
        formData.append('paidAt', payment.value.paid);

        files.value.forEach((file, index) => {
            formData.append(`file${index}`, file);
        });

        try {
            const response = await ApiAdminPaymentSave(props.invoice.id, {}, formData);
            const message  = payment.value.id ? 'Платёж обновлён' : `Платёж ${response.data.payment.id} создан`;
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
        clearError,
        closeAction,
        chooseFiles,
        fileCountExceed,
        fileElem,
        fileSizeExceed,
        files,
        filesSize,
        forcePaid,
        formatMoney,
        getAction,
        hideDialog,
        loading,
        makeAction,
        makePaid,
        onFileUpdated,
        onUpdatedCount,
        payment,
        paymentsCount,
        reloadList,
        saveAction,
        selectedId,
        showDialog,
    };
}
