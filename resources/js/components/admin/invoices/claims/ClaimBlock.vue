<template>
    <div>
        <h5>Услуги</h5>
        <div>
            <button
                class="btn btn-success mb-2"
                v-if="invoice.actions.claims.edit"
                @click="makeAction"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить услугу
            </button>
        </div>

        <claims-list
            :invoice-id="invoice.id"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
            v-model:count="claimCount"
            @update:count="onUpdatedCount"
        />

        <view-dialog
            v-model:show="showDialog"
            v-model:hide="hideDialog"
            @hidden="closeAction"
            v-if="claim && (claim.actions.edit || claim.actions.view)"
        >
            <template #title>
                {{ claim.id ? (claim.actions.edit ? 'Редактирование услуги' : 'Просмотр услуги') : 'Добавление услуги' }}
            </template>

            <template #body>
                <!-- Услуга -->
                <div class="mb-3">
                    <simple-select
                        v-model="claim.serviceId"
                        :disabled="selectedId || loading"
                        :options="servicesSelect"
                        label="Услуга"
                        :required="true"
                        @update:modelValue="onServiceIdChanged"
                    />
                </div>

                <!-- Своё название -->
                <div class="mb-3">
                    <custom-input
                        v-model="claim.name"
                        :errors="errors?.name"
                        label="Своё название услуги"
                        type="text"
                        :disabled="!claim.actions.edit || loading"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Тариф -->
                <div class="mb-3">
                    <custom-input
                        v-model="claim.tariff"
                        :errors="errors?.tariff"
                        label="Тариф"
                        type="number"
                        step="0.01"
                        :disabled="!claim.actions.edit || loading"
                        @update:modelValue="onTariffChanged"
                    />
                </div>

                <!-- Стоимость -->
                <div class="mb-3">
                    <custom-input
                        v-model="claim.cost"
                        :errors="errors?.cost"
                        label="Стоимость"
                        type="number"
                        step="0.01"
                        :disabled="!claim.actions.edit || loading"
                        @update:modelValue="onCostChanged"
                    />
                </div>
            </template>

            <template #footer v-if="claim.actions.edit">
                <div class="d-flex justify-content-end w-100">
                    <button
                        class="btn btn-success"
                        :disabled="!canSave || loading"
                        @click="saveAction"
                    >
                        <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        {{ claim.id ? 'Сохранить' : 'Создать' }}
                    </button>
                </div>
            </template>
        </view-dialog>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    computed,
    defineProps,
    defineEmits,
    defineOptions,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import SimpleSelect         from '../../../common/form/SimpleSelect.vue';
import CustomInput          from '../../../common/form/CustomInput.vue';
import ClaimsList           from './ClaimsList.vue';
import ViewDialog           from '../../../common/ViewDialog.vue';
import {
    ApiAdminClaimCreate,
    ApiAdminClaimView,
    ApiAdminClaimSave,
}                           from '@api';

defineOptions({
    name: 'ClaimBlock',
});

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
    reload : {
        type   : Boolean,
        default: false,
    },
    count  : {
        type   : Number,
        default: 0,
    },
});

const emit = defineEmits(['update:count', 'update:reload']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

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

// Возможность сохранения
const canSave = computed(() => {
    return claim.value && claim.value.serviceId && parseFloat(claim.value.cost) >= 0;
});

// Инициализация
const init = () => {
    claimCount.value = props.count || 0;
};

// Добавление услуги
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

// Получение услуги
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

// Сохранение
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

// Изменение услуги
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

// Изменение стоимости
const onCostChanged = () => {
    if (parseFloat(claim.value.tariff) > parseFloat(claim.value.cost)) {
        claim.value.tariff = parseFloat(claim.value.cost).toFixed(2);
    }
};

// Изменение тарифа
const onTariffChanged = () => {
    if (parseFloat(claim.value.tariff) > parseFloat(claim.value.cost)) {
        claim.value.cost = parseFloat(claim.value.tariff).toFixed(2);
    }
};

// Закрытие
const closeAction = () => {
    claim.value      = null;
    selectedId.value = null;
};

// После сохранения
const onSaved = () => {
    reloadList.value = true;
    emit('update:reload', true);
};

// Обновление количества
const onUpdatedCount = (value) => {
    claimCount.value = value;
    emit('update:count', value);
};

// Следим за перезагрузкой
watch(() => props.reload, (value) => {
    if (value) {
        reloadList.value = true;
    }
});

// Следим за выбранным ID
watch(selectedId, (value) => {
    if (value) {
        getAction();
    }
    else {
        claim.value = null;
    }
});

// Следим за скрытием диалога
watch(hideDialog, () => {
    closeAction();
});

// Следим за перезагрузкой списка
watch(reloadList, (value) => {
    emit('update:reload', value);
});

// Следим за количеством
watch(claimCount, (value) => {
    emit('update:count', value);
});

onMounted(init);
</script>