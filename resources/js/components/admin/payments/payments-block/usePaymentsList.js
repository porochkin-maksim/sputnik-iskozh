import {
    onMounted,
    ref,
    watch,
} from 'vue';

import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminNewPaymentDelete,
    ApiAdminNewPaymentList,
}                           from '@api';

export function usePaymentsList (props, emit) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();

    const payments    = ref([]);
    const actions     = ref({});
    const isLoading   = ref(false);
    const dropLoading = ref(null);

    const loadList = async () => {
        isLoading.value = true;
        try {
            const response = await ApiAdminNewPaymentList();
            actions.value  = response.data.actions || {};
            payments.value = response.data.payments || [];
            emit('update:count', payments.value.length);
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            isLoading.value = false;
        }
    };

    const editAction = (id) => {
        emit('update:selectedId', id);
    };

    const dropAction = async (id) => {
        if (!confirm('Удалить платёж?')) {
            return;
        }

        dropLoading.value = id;
        try {
            const response = await ApiAdminNewPaymentDelete(id);
            if (response.data) {
                await loadList();
                showInfo('Платёж удалён');
            }
            else {
                showDanger('Платёж не удалён');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            dropLoading.value = null;
        }
    };

    const onFileUpdated = () => {
        loadList();
    };

    watch(() => props.reload, (value) => {
        if (value) {
            loadList();
            emit('update:reload', false);
        }
    });

    onMounted(loadList);

    return {
        actions,
        dropAction,
        dropLoading,
        editAction,
        isLoading,
        loadList,
        onFileUpdated,
        payments,
    };
}
