import {
    ref,
    computed,
    onMounted,
    onUnmounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminInvoiceImportPaymentsParseFile,
    ApiAdminInvoiceImportPaymentsSave,
}                           from '@api';

export function usePeriodPaymentsImportBlock (props, controlsRef) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();

    const mode  = ref('single');
    const files = ref({
        main: null,
        prev: null,
    });

    const loading          = ref(false);
    const submitting       = ref(false);
    const importData       = ref(null);
    const editedAmounts    = ref({});
    const error            = ref(null);
    const activeTab        = ref(0);
    const loadingStartTime = ref(null);
    const loadingText      = ref('Обработка файла...');
    const autoFillStrategy = ref({});

    const fillStrategies = [
        { value: 'manual', label: 'Ручной ввод' },
        { value: 'difference', label: 'Разница "оплачено"' },
        { value: 'invoiceDelta', label: 'Долг из базы' },
        { value: 'importDebt', label: 'Долг из импорта' },
        { value: 'maxDebt', label: 'Максимальный долг' },
        { value: 'minDebt', label: 'Минимальный долг' },
        { value: 'zero', label: 'Обнулить' },
    ];

    const columns = ref({
        accrued: 'D',
        paid   : 'E',
        debt   : 'F',
    });

    const isColumnsValid = computed(() => columns.value.accrued.trim() !== '' &&
        columns.value.paid.trim() !== '' &&
        columns.value.debt.trim() !== '');

    const canUpload = computed(() => {
        if (!isColumnsValid.value) {
            return false;
        }
        if (mode.value === 'single') {
            return !!files.value.main;
        }
        return !!files.value.main && !!files.value.prev;
    });

    const getKey = (district, item) => `${district}:${item.invoiceId}`;

    let loadingInterval = null;

    const startTimer = (isSaving = false) => {
        loadingStartTime.value = Date.now();
        if (loadingInterval) {
            clearInterval(loadingInterval);
        }
        loadingInterval = setInterval(() => {
            if (!loading.value && !submitting.value) {
                clearInterval(loadingInterval);
                return;
            }
            const elapsed     = Math.floor((Date.now() - loadingStartTime.value) / 1000);
            const minutes     = Math.floor(elapsed / 60);
            const seconds     = elapsed % 60;
            const timeStr     = minutes > 0 ? `${minutes} мин ${seconds} сек` : `${seconds} сек`;
            loadingText.value = isSaving
                ? `Сохранение платежей... (${timeStr})`
                : `Обработка файлов... (${timeStr})`;
        }, 1000);
    };

    const stopTimer = () => {
        if (loadingInterval) {
            clearInterval(loadingInterval);
        }
        loadingStartTime.value = null;
        loadingText.value      = 'Обработка файлов...';
    };

    const onFileSelected = (type, event) => {
        const file = event.target.files[0];
        if (!file) {
            return;
        }
        files.value[type] = file;
    };

    const applyAutoFill = (district) => {
        const districtData = importData.value?.find(d => d.district === district);
        if (!districtData) {
            return;
        }

        const strategy = autoFillStrategy.value[district] || 'manual';
        districtData.items.forEach(item => {
            const key     = getKey(district, item);
            let newAmount = 0;
            switch (strategy) {
                case 'difference':
                    newAmount = Math.max(0, item.paid - item.invoicePaid);
                    break;
                case 'importDebt':
                    newAmount = item.debt;
                    break;
                case 'invoiceDelta':
                    newAmount = item.invoiceDelta;
                    break;
                case 'maxDebt':
                    newAmount = Math.max(item.invoiceDelta, item.debt);
                    break;
                case 'minDebt':
                    newAmount = Math.min(item.invoiceDelta, item.debt);
                    break;
                case 'zero':
                    newAmount = 0;
                    break;
                default:
                    return;
            }
            if (newAmount < 0) {
                newAmount = 0;
            }
            editedAmounts.value[key] = newAmount;
        });
    };

    const validateAmount = (district, item) => {
        const key  = getKey(district, item);
        let amount = editedAmounts.value[key];
        if (amount === null || amount === undefined) {
            return;
        }

        const maxAmount = Math.max(item.invoiceDebt, item.debt);
        if (amount < 0) {
            editedAmounts.value[key] = 0;
        }
        if (amount > maxAmount) {
            editedAmounts.value[key] = maxAmount;
        }
    };

    const canSubmit = computed(() => {
        if (!importData.value) {
            return false;
        }
        for (const districtData of importData.value) {
            for (const item of districtData.items) {
                const key    = getKey(districtData.district, item);
                const amount = editedAmounts.value[key];
                if (amount < 0) {
                    return false;
                }
            }
        }
        return true;
    });

    const uploadFiles = async () => {
        if (!canUpload.value) {
            return;
        }

        loading.value = true;
        startTimer(false);
        error.value         = null;
        importData.value    = null;
        editedAmounts.value = {};

        const formData = new FormData();
        formData.append('col_accrued', columns.value.accrued);
        formData.append('col_paid', columns.value.paid);
        formData.append('col_debt', columns.value.debt);
        formData.append('mode', mode.value);
        formData.append('file_main', files.value.main);
        if (mode.value === 'diff') {
            formData.append('file_prev', files.value.prev);
        }

        try {
            const response   = await ApiAdminInvoiceImportPaymentsParseFile(props.periodId, {}, formData);
            importData.value = response.data;

            for (const districtData of importData.value) {
                const district = districtData.district;
                if (!autoFillStrategy.value[district]) {
                    autoFillStrategy.value[district] = 'difference';
                }
                applyAutoFill(district);
            }
        }
        catch (err) {
            error.value = err.response?.data?.message || 'Ошибка при загрузке файлов';
            parseResponseErrors(err);
        }
        finally {
            loading.value = false;
            stopTimer();
            files.value = { main: null, prev: null };
            controlsRef.value?.resetFileInputs?.();
        }
    };

    const submitPayments = async (name) => {
        if (!canSubmit.value) {
            return;
        }

        if (!confirm('Сохранить данные? Это действие необратимо')) {
            return;
        }

        submitting.value = true;
        startTimer(true);
        const payload = { payments: [], name };

        for (const districtData of importData.value) {
            for (const item of districtData.items) {
                const key    = getKey(districtData.district, item);
                const amount = editedAmounts.value[key];
                if (amount > 0) {
                    payload.payments.push({
                        invoice_id: item.invoiceId,
                        amount,
                    });
                }
            }
        }

        if (payload.payments.length === 0) {
            showInfo('Нет платежей для сохранения');
            submitting.value = false;
            stopTimer();
            return;
        }

        try {
            await ApiAdminInvoiceImportPaymentsSave(props.periodId, {}, payload);
            showInfo('Платежи будут сохранены в фоне');
            importData.value    = null;
            editedAmounts.value = {};
        }
        catch (err) {
            const message = err.response?.data?.message || 'Ошибка при сохранении платежей';
            showDanger(message);
            parseResponseErrors(err);
        }
        finally {
            submitting.value = false;
            stopTimer();
        }
    };

    const handleBeforeUnload = (e) => {
        if (submitting.value) {
            e.preventDefault();
            e.returnValue = 'Идёт сохранение платежей. Вы уверены, что хотите покинуть страницу?';
            return e.returnValue;
        }
    };

    onMounted(() => {
        window.addEventListener('beforeunload', handleBeforeUnload);
    });

    onUnmounted(() => {
        window.removeEventListener('beforeunload', handleBeforeUnload);
        stopTimer();
    });

    return {
        activeTab,
        autoFillStrategy,
        applyAutoFill,
        canSubmit,
        canUpload,
        columns,
        editedAmounts,
        error,
        fillStrategies,
        files,
        getKey,
        importData,
        isColumnsValid,
        loading,
        loadingText,
        mode,
        onFileSelected,
        parseResponseErrors,
        startTimer,
        stopTimer,
        submitPayments,
        submitting,
        uploadFiles,
        validateAmount,
    };
}
