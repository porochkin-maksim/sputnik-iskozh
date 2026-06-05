import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminHelpDeskSettingsServicesList,
    ApiAdminHelpDeskSettingsServicesCreate,
    ApiAdminHelpDeskSettingsServicesSave,
    ApiAdminHelpDeskSettingsServicesDelete,
}                           from '@api';

export function useTicketServiceBlock (props) {
    const { parseResponseErrors, showInfo, showSuccess, showDanger } = useResponseError();

    const services         = ref([]);
    const loadingServices  = ref(false);
    const saving           = ref(false);
    const editingServiceId = ref(null);
    const editingTempId    = ref(null);
    const formData         = ref({
        id        : null,
        name      : '',
        code      : '',
        sort_order: 0,
        is_active : true,
    });

    let tempIdCounter = 0;

    const hasEditingService = computed(() => editingServiceId.value !== null);

    const loadServices = async () => {
        if (!props.categoryId) {
            return;
        }

        loadingServices.value = true;
        try {
            const response = await ApiAdminHelpDeskSettingsServicesList(props.categoryId);
            services.value = response.data.services || [];
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            loadingServices.value = false;
        }
    };

    const createService = async () => {
        if (hasEditingService.value) {
            showDanger('Сначала сохраните или отмените текущее редактирование');
            return;
        }

        try {
            const response    = await ApiAdminHelpDeskSettingsServicesCreate(props.categoryId);
            const newService  = response.data.service;
            newService.tempId = --tempIdCounter;
            services.value.unshift(newService);
            editingServiceId.value = newService.tempId;
            editingTempId.value    = newService.tempId;
            formData.value         = {
                id        : newService.id,
                name      : newService.name,
                code      : newService.code,
                sort_order: newService.sort_order,
                is_active : newService.is_active,
            };
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const editService = (service) => {
        if (hasEditingService.value && editingServiceId.value !== (service.id || service.tempId)) {
            showDanger('Сначала сохраните или отмените текущее редактирование');
            return;
        }

        editingServiceId.value = service.id || service.tempId;
        editingTempId.value    = service.tempId || null;
        formData.value         = {
            id        : service.id,
            name      : service.name,
            code      : service.code,
            sort_order: service.sort_order,
            is_active : service.is_active,
        };
    };

    const cancelEdit = () => {
        if (editingTempId.value && !formData.value.id) {
            services.value = services.value.filter(service => service.tempId !== editingTempId.value);
        }

        editingServiceId.value = null;
        editingTempId.value    = null;
    };

    const saveService = async () => {
        saving.value = true;
        try {
            const payload = {
                id         : formData.value.id,
                category_id: props.categoryId,
                name       : formData.value.name,
                code       : formData.value.code,
                sort_order : formData.value.sort_order,
                is_active  : formData.value.is_active,
            };

            const response = await ApiAdminHelpDeskSettingsServicesSave({}, payload);
            const saved    = response.data.service;

            let index = -1;
            if (saved.id) {
                index = services.value.findIndex(service => service.id === saved.id);
            }
            if (index === -1 && editingTempId.value) {
                index = services.value.findIndex(service => service.tempId === editingTempId.value);
            }

            if (index !== -1) {
                services.value[index] = saved;
            }
            else {
                services.value.push(saved);
            }

            editingServiceId.value = null;
            editingTempId.value    = null;
            showSuccess('Услуга сохранена');
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            saving.value = false;
        }
    };

    const deleteService = async (service) => {
        if (!confirm(`Удалить услугу "${service.name}"?`)) {
            return;
        }

        if (!service.id) {
            services.value = services.value.filter(item => item.tempId !== service.tempId);
            if (editingServiceId.value === service.tempId) {
                editingServiceId.value = null;
                editingTempId.value    = null;
            }
            return;
        }

        try {
            await ApiAdminHelpDeskSettingsServicesDelete(service.id);
            services.value = services.value.filter(item => item.id !== service.id);
            if (editingServiceId.value === service.id) {
                editingServiceId.value = null;
                editingTempId.value    = null;
            }
            showInfo('Услуга удалена');
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    watch(() => props.categoryId, (newId, oldId) => {
        if (newId && newId !== oldId) {
            loadServices();
            editingServiceId.value = null;
            editingTempId.value    = null;
        }
    });

    onMounted(() => {
        if (props.categoryId) {
            loadServices();
        }
    });

    return {
        cancelEdit,
        createService,
        deleteService,
        editService,
        editingServiceId,
        editingTempId,
        formData,
        hasEditingService,
        loadingServices,
        saveService,
        services,
        saving,
    };
}
