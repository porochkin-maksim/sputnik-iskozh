import {
    computed,
    reactive,
    ref,
    watch,
} from 'vue';

import { useResponseError }    from '@composables/useResponseError';
import { ApiAdminAccountSave } from '@api';

export function useAccountItemAdd (props, emit) {
    const {
              clearResponseErrors,
              parseResponseErrors,
              showInfo,
              showDanger,
          } = useResponseError();

    const loading    = ref(false);
    const showDialog = ref(false);
    const hideDialog = ref(false);

    const formData = reactive({
        number        : null,
        size          : null,
        isInvoicing   : false,
        cadastreNumber: null,
    });

    const resetForm = () => {
        formData.number         = null;
        formData.size           = null;
        formData.isInvoicing    = false;
        formData.cadastreNumber = null;
        clearResponseErrors();
    };

    const initForm = async () => {
        await resetForm();
        if (props.modelValue) {
            formData.number         = props.modelValue.number;
            formData.size           = props.modelValue.size;
            formData.isInvoicing    = props.modelValue.is_invoicing;
            formData.cadastreNumber = props.modelValue.cadastreNumber;
        }

        showDialog.value = true;
        hideDialog.value = false;
    };

    const canSave = computed(() => {
        return Boolean(formData.number && formData.size !== null && formData.size >= 0);
    });

    const onCloseDialog = () => {
        showDialog.value = false;
        hideDialog.value = true;
        loading.value    = false;
        resetForm();
    };

    const saveAction = async () => {
        loading.value = true;
        clearResponseErrors();

        const form = new FormData();
        form.append('number', formData.number);
        form.append('size', parseInt(formData.size ? formData.size : 0));
        form.append('is_invoicing', !!formData.isInvoicing);
        form.append('cadastreNumber', formData.cadastreNumber || '');

        try {
            const response = await ApiAdminAccountSave({}, form);
            showInfo('Участок ' + response.data.account.id + ' создан');
            emit('updated', response.data.account);
            onCloseDialog();
        }
        catch (error) {
            const text = error?.response?.data?.message || 'Не получилось создать участок';
            showDanger(text);
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    watch(() => props.modelValue, (newVal) => {
        if (newVal) {
            initForm();
        }
    }, { immediate: true });

    return {
        canSave,
        formData,
        hideDialog,
        initForm,
        loading,
        onCloseDialog,
        resetForm,
        saveAction,
        showDialog,
    };
}
