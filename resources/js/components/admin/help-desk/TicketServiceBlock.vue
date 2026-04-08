<template>
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Услуги категории</h5>
                <button class="btn btn-sm btn-primary" :disabled="hasEditingService" @click="createService">
                    <i class="fa fa-plus"></i> Добавить услугу
                </button>
            </div>
        </div>

        <div class="card-body">
            <loading-spinner
                v-if="loadingServices"
                size="sm"
                color="secondary"
                text="Загрузка услуг..."
                wrapper-class="py-3"
            />

            <div v-else-if="services.length === 0" class="text-muted">
                Услуг пока нет. Добавьте первую.
            </div>

            <div v-else>
                <div
                    v-for="(service, index) in services"
                    :key="service.tempId || service.id"
                    :class="[index === services.length - 1 ? '' : 'border-bottom pb-2 mb-2']"
                >
                    <!-- Режим просмотра -->
                    <template v-if="editingServiceId !== (service.id || service.tempId)">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ service.name }}</strong>
                                <span v-if="!service.is_active" class="badge bg-secondary ms-2">Неактивна</span>
                                <br>
                                <small class="text-muted">Код: {{ service.code }}</small>
                                <small class="text-muted ms-2">Порядок: {{ service.sort_order }}</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" :disabled="hasEditingService"
                                        @click="editService(service)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger" :disabled="hasEditingService"
                                        @click="deleteService(service)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Режим редактирования -->
                    <template v-else>
                        <form @submit.prevent="saveService">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <custom-input
                                            v-model="formData.name"
                                            label="Название услуги"
                                            required
                                            :disabled="saving"
                                        />
                                    </div>
                                    <div class="mb-2">
                                        <custom-input
                                            v-model="formData.sort_order"
                                            label="Порядок сортировки"
                                            type="number"
                                            :disabled="saving"
                                        />
                                    </div>
                                    <div class="mb-2 d-flex align-items-center">
                                        <custom-checkbox
                                            v-model="formData.is_active"
                                            label="Активна"
                                            switch-style
                                            :disabled="saving"
                                        />
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary" :disabled="saving">
                                            <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                                            {{ saving ? 'Сохранение...' : 'Сохранить' }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                @click="cancelEdit">
                                            Отмена
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    computed,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import CustomInput          from '../../common/form/CustomInput.vue';
import CustomCheckbox       from '../../common/form/CustomCheckbox.vue';
import {
    ApiAdminHelpDeskSettingsServicesList,
    ApiAdminHelpDeskSettingsServicesCreate,
    ApiAdminHelpDeskSettingsServicesSave,
    ApiAdminHelpDeskSettingsServicesDelete,
}                           from '@api';

const props = defineProps({
    categoryId: {
        type    : Number,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showSuccess, showDanger } = useResponseError();

const services         = ref([]);
const loadingServices  = ref(false);
const saving           = ref(false);
const editingServiceId = ref(null);
const editingTempId    = ref(null); // запоминаем временный id для новой услуги
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
        // Открываем редактирование
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
    // Если редактировали новую услугу (без id), удаляем её из списка
    if (editingTempId.value && !formData.value.id) {
        services.value = services.value.filter(s => s.tempId !== editingTempId.value);
    }
    editingServiceId.value = null;
    editingTempId.value    = null;
};

const saveService = async () => {
    saving.value = true;
    try {
        const payload  = {
            id         : formData.value.id,
            category_id: props.categoryId,
            name       : formData.value.name,
            code       : formData.value.code,
            sort_order : formData.value.sort_order,
            is_active  : formData.value.is_active,
        };
        const response = await ApiAdminHelpDeskSettingsServicesSave({}, payload);
        const saved    = response.data.service;

        // Поиск индекса для обновления
        let index = -1;
        if (saved.id) {
            index = services.value.findIndex(s => s.id === saved.id);
        }
        if (index === -1 && editingTempId.value) {
            index = services.value.findIndex(s => s.tempId === editingTempId.value);
        }
        if (index !== -1) {
            // Заменяем элемент
            services.value[index] = saved;
        }
        else {
            // Если не нашли, добавляем в конец (хотя такого не должно быть)
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
        // Новая услуга, просто удаляем из списка
        services.value = services.value.filter(s => s.tempId !== service.tempId);
        if (editingServiceId.value === service.tempId) {
            editingServiceId.value = null;
            editingTempId.value    = null;
        }
        return;
    }

    try {
        await ApiAdminHelpDeskSettingsServicesDelete(service.id);
        services.value = services.value.filter(s => s.id !== service.id);
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
</script>