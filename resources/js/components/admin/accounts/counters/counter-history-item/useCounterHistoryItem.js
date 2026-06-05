import {
    computed,
    onMounted,
    reactive,
    ref,
} from 'vue';

import { useResponseError }        from '@composables/useResponseError';
import { ApiAdminCounterAddValue } from '@api';

export function useCounterHistoryItem (props, emit) {
    const { parseResponseErrors, showSuccess, clearResponseErrors } = useResponseError();

    const loading    = ref(false);
    const showDialog = ref(true);
    const hideDialog = ref(false);
    const file       = ref(null);
    const fileElem   = ref(null);

    const formData = reactive({
        id   : null,
        value: null,
        date : '',
    });

    const localHistory = computed(() => props.history);

    const initForm = () => {
        if (props.history) {
            formData.id    = props.history.id;
            formData.value = props.history.value;
            formData.date  = props.history.date;
            return;
        }

        const date     = new Date();
        formData.id    = null;
        formData.value = props.counter?.value ?? null;
        formData.date  = props.counter?.date ?? date.toISOString().split('T')[0];
    };

    const canSubmitAction = computed(() => {
        return !loading.value && formData.value && formData.date;
    });

    const chooseFile = () => {
        fileElem.value?.click();
    };

    const appendFile = (event) => {
        file.value = event.target.files?.[0] ?? null;
    };

    const removeFile = () => {
        file.value = null;
    };

    const onSuccessSubmit = () => {
        showDialog.value = false;
        hideDialog.value = true;
        file.value       = null;
        emit('historyUpdated');
    };

    const closeAction = () => {
        showDialog.value = false;
        emit('historyUpdated');
    };

    const saveAction = async () => {
        if (!props.counter?.id) {
            return;
        }

        loading.value = true;
        clearResponseErrors();

        const form = new FormData();
        form.append('counter_id', props.counter.id);
        form.append('id', formData.id);
        form.append('value', formData.value);
        form.append('date', formData.date);

        if (file.value) {
            form.append('file', file.value);
        }

        try {
            await ApiAdminCounterAddValue({}, form);
            onSuccessSubmit();
            showSuccess(formData.id ? 'Показания обновлены' : 'Показания добавлены');
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    onMounted(initForm);

    return {
        appendFile,
        canSubmitAction,
        chooseFile,
        closeAction,
        file,
        fileElem,
        formData,
        hideDialog,
        initForm,
        localHistory,
        onSuccessSubmit,
        removeFile,
        saveAction,
        showDialog,
    };
}
