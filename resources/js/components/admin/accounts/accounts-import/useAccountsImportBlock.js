import {
    ref,
    computed,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminAccountImportParseFile,
    ApiAdminAccountImportSave,
}                           from '@api';

export function useAccountsImportBlock () {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();

    const file       = ref(null);
    const loading    = ref(false);
    const submitting = ref(false);
    const items      = ref([]);
    const total      = ref(0);
    const changes    = ref(0);
    const error      = ref(null);
    const submitted  = ref(false);
    const parsed     = ref(false);

    const canUpload = computed(() => !!file.value);

    const canSubmit = computed(() => items.value.length > 0 && !submitted.value);

    const uploadFiles = async () => {
        if (!canUpload.value) {
            return;
        }

        loading.value   = true;
        parsed.value    = false;
        error.value     = null;
        items.value     = [];
        total.value     = 0;
        changes.value   = 0;
        submitted.value = false;

        const formData = new FormData();
        formData.append('file', file.value);

        try {
            const response = await ApiAdminAccountImportParseFile({}, formData);
            const data     = response.data;
            items.value    = data.items || [];
            total.value    = data.total || 0;
            changes.value  = data.changes || 0;
            parsed.value   = true;
        }
        catch (err) {
            error.value = err.response?.data?.error || 'Ошибка при загрузке файла';
            parseResponseErrors(err);
        }
        finally {
            loading.value = false;
            file.value    = null;
        }
    };

    const submitAccounts = async () => {
        if (!canSubmit.value) {
            return;
        }

        if (!confirm('Сохранить изменения? Это действие запустит фоновый импорт.')) {
            return;
        }

        submitting.value = true;
        error.value      = null;

        try {
            await ApiAdminAccountImportSave({}, { accounts: items.value });
            showInfo('Участки сохранены');
            submitted.value = true;
            items.value     = [];
            total.value     = 0;
            changes.value   = 0;
        }
        catch (err) {
            const message = err.response?.data?.error || 'Ошибка при сохранении участков';
            showDanger(message);
            parseResponseErrors(err);
        }
        finally {
            submitting.value = false;
        }
    };

    return {
        file,
        loading,
        submitting,
        items,
        total,
        changes,
        error,
        submitted,
        parsed,
        canUpload,
        canSubmit,
        uploadFiles,
        submitAccounts,
    };
}
