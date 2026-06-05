import {
    computed,
    onMounted,
    reactive,
    ref,
    watch,
} from 'vue';

import { useResponseError }    from '@composables/useResponseError';
import { ApiAdminCounterSave } from '@api';

export function useCounterItem (props, emit) {
    const { parseResponseErrors, showInfo, clearResponseErrors } = useResponseError();

    const loading          = ref(false);
    const showDialog       = ref(false);
    const hideDialog       = ref(false);
    const file             = ref(null);
    const passportFile     = ref(null);
    const fileElem         = ref(null);
    const filePassportElem = ref(null);

    const localCounter = reactive({
        id         : null,
        number     : null,
        isInvoicing: false,
        increment  : 0,
        value      : null,
        expireAt   : null,
    });

    const canSubmitAction = computed(() => {
        return !!localCounter.number && (localCounter.id || localCounter.value);
    });

    const assignCounter = (counter) => {
        resetForm();

        if (!counter) {
            return;
        }

        Object.assign(localCounter, { ...counter });
    };

    const resetForm = () => {
        localCounter.id          = null;
        localCounter.number      = null;
        localCounter.isInvoicing = false;
        localCounter.increment   = 0;
        localCounter.value       = null;
        localCounter.expireAt    = null;
        file.value               = null;
        passportFile.value       = null;
    };

    const initForm = () => {
        clearResponseErrors();
        assignCounter(props.counter);
        showDialog.value = props.showForm;
        hideDialog.value = false;
    };

    const chooseFile = () => {
        fileElem.value?.click();
    };

    const appendFile = (event) => {
        file.value = event.target.files?.[0] ?? null;
    };

    const removeFile = () => {
        file.value = null;
    };

    const choosePassportFile = () => {
        filePassportElem.value?.click();
    };

    const appendPassportFile = (event) => {
        passportFile.value = event.target.files?.[0] ?? null;
    };

    const removePassportFile = () => {
        passportFile.value = null;
    };

    const calculateIncrement = () => {
        if (localCounter.increment < 0) {
            localCounter.increment = -localCounter.increment;
        }
    };

    const closeAction = () => {
        showDialog.value = false;
        emit('counterUpdated');
    };

    const onSuccessSubmit = () => {
        showDialog.value   = false;
        hideDialog.value   = true;
        file.value         = null;
        passportFile.value = null;
        emit('counterUpdated');
    };

    const saveAction = async () => {
        if (!props.account?.id) {
            return;
        }

        loading.value = true;
        clearResponseErrors();

        const form = new FormData();
        form.append('accountId', props.account.id);
        form.append('id', localCounter.id);
        form.append('number', localCounter.number);
        form.append('isInvoicing', localCounter.isInvoicing);

        if (localCounter.value !== undefined && localCounter.value !== null) {
            form.append('value', localCounter.value);
        }

        form.append('expireAt', localCounter.expireAt ? String(localCounter.expireAt) : '');
        form.append('increment', localCounter.increment);

        if (file.value) {
            form.append('file', file.value);
        }

        if (passportFile.value) {
            form.append('passportFile', passportFile.value);
        }

        try {
            await ApiAdminCounterSave({}, form);
            onSuccessSubmit();
            showInfo(localCounter.id ? 'Счётчик обновлён' : 'Счётчик добавлен');
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    watch(() => props.counter, () => {
        if (props.showForm) {
            initForm();
        }
    }, { immediate: true, deep: true });

    watch(() => props.showForm, (showForm) => {
        if (showForm) {
            initForm();
        }
        else {
            resetForm();
        }
    }, { immediate: true });

    onMounted(initForm);

    return {
        calculateIncrement,
        canSubmitAction,
        closeAction,
        file,
        fileElem,
        filePassportElem,
        hideDialog,
        initForm,
        loading,
        localCounter,
        onSuccessSubmit,
        passportFile,
        removeFile,
        removePassportFile,
        saveAction,
        chooseFile,
        choosePassportFile,
        showDialog,
        appendFile,
        appendPassportFile,
    };
}
