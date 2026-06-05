import {
    computed,
    onMounted,
    reactive,
    ref,
} from 'vue';

import { usePermissions }      from '@composables/usePermissions';
import { useResponseError }    from '@composables/useResponseError';
import { ApiAdminAccountSave } from '@api';

export function useAccountItemView (props) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { has }                                       = usePermissions();

    const canEdit        = computed(() => has('accounts', 'edit'));
    const canUserView    = computed(() => has('users', 'view'));
    const canInvoiceView = computed(() => has('invoices', 'view'));
    const canCounterView = computed(() => has('counters', 'view'));

    const loading = ref(false);
    const account = ref({});

    const formData = reactive({
        id            : null,
        number        : '',
        size          : null,
        isInvoicing   : false,
        cadastreNumber: '',
    });

    const initForm = () => {
        account.value           = props.modelValue;
        formData.id             = account.value.id;
        formData.number         = account.value.number || '';
        formData.size           = account.value.size || null;
        formData.isInvoicing    = account.value.isInvoicing || false;
        formData.cadastreNumber = account.value.cadastreNumber || '';
    };

    const canSave = computed(() => {
        return canEdit.value && formData.number && formData.size !== null && formData.size >= 0;
    });

    const saveAction = async () => {
        loading.value = true;

        const form = new FormData();
        form.append('id', formData.id);
        form.append('number', formData.number);
        form.append('size', parseInt(formData.size ? formData.size : 0));
        form.append('is_invoicing', !!formData.isInvoicing);
        form.append('cadastreNumber', formData.cadastreNumber);

        try {
            const response = await ApiAdminAccountSave({}, form);
            account.value  = {
                ...account.value,
                ...response.data,
            };
            showInfo('Участок обновлён');
        }
        catch (error) {
            const text = error?.response?.data?.message || 'Не получилось сохранить участок';
            showDanger(text);
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        initForm();
    });

    return {
        account,
        canCounterView,
        canEdit,
        canInvoiceView,
        canSave,
        canUserView,
        formData,
        loading,
        saveAction,
    };
}
