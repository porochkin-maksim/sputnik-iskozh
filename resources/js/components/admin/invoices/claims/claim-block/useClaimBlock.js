import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { usePermissions }   from '@composables/usePermissions';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminClaimCreate,
    ApiAdminClaimView,
    ApiAdminClaimSave,
}                           from '@api';

export function useClaimBlock (props, emit) {
    const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { has }                                                           = usePermissions();

    const claimCount     = ref(0);
    const actions        = ref({});
    const reloadList     = ref(false);
    const claim          = ref(null);
    const selectedId     = ref(null);
    const servicesSelect = ref([]);
    const services       = ref([]);
    const showDialog     = ref(false);
    const hideDialog     = ref(false);
    const loading        = ref(false);

    const canEdit = computed(() => has('invoice_services', 'edit'));
    const canSave = computed(() => claim.value && claim.value.serviceId && parseFloat(claim.value.cost) >= 0);

    const init = () => {
        claimCount.value = props.count || 0;
    };

    const onServiceIdChanged = async (id) => {
        if (!services.value || !id) {
            return;
        }

        const foundService = Object.values(services.value).find(
            service => parseInt(service.id) === parseInt(id),
        );

        if (foundService) {
            claim.value.tariff = parseFloat(foundService.cost).toFixed(2);
            claim.value.cost   = parseFloat(foundService.cost).toFixed(2);
        }
    };

    const onCostChanged = () => {
        if (parseFloat(claim.value.tariff) > parseFloat(claim.value.cost)) {
            claim.value.tariff = parseFloat(claim.value.cost).toFixed(2);
        }
    };

    const onTariffChanged = () => {
        if (parseFloat(claim.value.tariff) > parseFloat(claim.value.cost)) {
            claim.value.cost = parseFloat(claim.value.tariff).toFixed(2);
        }
    };

    const onSaved = () => {
        reloadList.value = true;
        emit('update:reload', true);
    };

    const onUpdatedCount = (value) => {
        claimCount.value = value;
        emit('update:count', value);
    };

    const makeAction = async () => {
        selectedId.value = null;

        try {
            const response       = await ApiAdminClaimCreate(props.invoice.id);
            servicesSelect.value = response.data.servicesSelect || [];
            services.value       = response.data.services || [];

            if (servicesSelect.value.length) {
                claim.value        = response.data.claim;
                claim.value.tariff = parseFloat(claim.value.tariff).toFixed(2);
                claim.value.cost   = parseFloat(claim.value.cost).toFixed(2);
                claim.value.paid   = parseFloat(claim.value.paid).toFixed(2);
                await onServiceIdChanged(claim.value.serviceId);
                showDialog.value = true;
            }
            else {
                claim.value = null;
                showInfo('Невозможно добавить услугу. Нет доступных услуг для добавления.');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const getAction = async () => {
        try {
            const response       = await ApiAdminClaimView(props.invoice.id, selectedId.value);
            servicesSelect.value = response.data.servicesSelect || [];
            claim.value          = response.data.claim;
            claim.value.tariff   = parseFloat(claim.value.tariff).toFixed(2);
            claim.value.cost     = parseFloat(claim.value.cost).toFixed(2);
            claim.value.paid     = parseFloat(claim.value.paid).toFixed(2);
            showDialog.value     = true;
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const saveAction = async () => {
        loading.value = true;

        const data = {
            id        : claim.value.id,
            invoice_id: props.invoice.id,
            service_id: claim.value.serviceId,
            tariff    : parseFloat(claim.value.tariff),
            cost      : parseFloat(claim.value.cost),
            name      : claim.value.name,
        };

        try {
            const response = await ApiAdminClaimSave(props.invoice.id, data);
            const message  = claim.value.id ? 'Услуга обновлена' : `Услуга ${response.data.claim.id} создана`;
            showInfo(message);
            claim.value = null;
            onSaved();
            showDialog.value = false;
        }
        catch (error) {
            const message = error?.response?.data?.message ||
                `Не удалось ${claim.value.id ? 'сохранить' : 'создать'} услугу`;
            showDanger(message);
            parseResponseErrors(error);
        }
        finally {
            loading.value    = false;
            selectedId.value = null;
        }
    };

    const closeAction = () => {
        claim.value      = null;
        selectedId.value = null;
        showDialog.value = false;
        hideDialog.value = false;
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
            claim.value = null;
        }
    });

    watch(hideDialog, () => {
        closeAction();
    });

    watch(reloadList, (value) => {
        emit('update:reload', value);
    });

    watch(claimCount, (value) => {
        emit('update:count', value);
    });

    onMounted(init);

    return {
        actions,
        canEdit,
        canSave,
        claim,
        claimCount,
        clearError,
        closeAction,
        errors,
        hideDialog,
        loading,
        makeAction,
        onCostChanged,
        onServiceIdChanged,
        onTariffChanged,
        onUpdatedCount,
        reloadList,
        saveAction,
        selectedId,
        servicesSelect,
        showDanger,
        showDialog,
    };
}
