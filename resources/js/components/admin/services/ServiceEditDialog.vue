<template>
    <view-dialog
        v-model:show="showDialog"
        v-model:hide="hideDialog"
        @hidden="closeDialog"
    >
        <template #title>
            {{ props.modelValue?.id ? 'Редактирование услуги' : 'Создание услуги' }}
        </template>
        <template #body>
            <div class="container-fluid">
                <!-- Название -->
                <div>
                    <custom-input
                        v-model="name"
                        :errors="errors?.name"
                        type="text"
                        label="Название"
                        :required="true"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Стоимость -->
                <div class="mt-2">
                    <custom-input
                        v-model="cost"
                        :errors="errors?.cost"
                        type="number"
                        step="0.01"
                        label="Стоимость"
                        :required="true"
                        @update:modelValue="clearError('cost')"
                    />
                </div>

                <!-- Период -->
                <div class="mt-2">
                    <custom-select
                        v-model="periodId"
                        :errors="errors?.period_id"
                        label="Период"
                        :options="props.periods"
                        :required="true"
                        :disabled="!actions?.period"
                        @update:modelValue="onPeriodChange"
                    />
                </div>

                <!-- Тип -->
                <div class="mt-2">
                    <custom-select
                        v-model="type"
                        :errors="errors?.type"
                        label="Тип"
                        :options="typeOptions"
                        :required="true"
                        :disabled="!actions?.type"
                        @update:modelValue="clearError('type')"
                    />
                </div>

                <!-- Активность (скрыто, но оставлено для совместимости) -->
                <div class="form-check d-none">
                    <custom-checkbox
                        v-model="active"
                        label="Активна"
                        :disabled="!actions?.active"
                    />
                </div>
            </div>
        </template>
        <template #footer>
            <button
                class="btn btn-success"
                :disabled="!canSave || loading"
                @click="saveAction"
            >
                <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ props.modelValue?.id ? 'Сохранить' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    defineProps,
    defineEmits,
}                              from 'vue';
import { useResponseError }    from '@composables/useResponseError';
import ViewDialog              from '../../common/ViewDialog.vue';
import CustomInput             from '@common/form/CustomInput.vue';
import CustomCheckbox          from '@common/form/CustomCheckbox.vue';
import { ApiAdminServiceSave } from '@api';
import CustomSelect            from '@common/form/CustomSelect.vue';

const props = defineProps({
    modelValue: {
        type   : Object,
        default: null,
    },
    show      : {
        type   : Boolean,
        default: false,
    },
    types     : {
        type    : Object,
        required: true,
    },
    periods   : {
        type    : Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'update:show']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

const id         = ref(null);
const name       = ref(null);
const type       = ref(null);
const periodId   = ref(null);
const cost       = ref(null);
const active     = ref(false);
const actions    = ref(null);
const loading    = ref(false);
const hideDialog = ref(false);

// Двусторонняя привязка show
const showDialog = computed({
    get: () => props.show,
    set: (value) => emit('update:show', value),
});

// Опции для типа (полное соответствие исходной логике)
const typeOptions = computed(() => {
    // Если actions.type === false - показываем все типы
    if (actions.value?.type === false) {
        return props.types.all || [];
    }
    // Иначе показываем доступные типы для выбранного периода
    return (props.types.available && props.types.available[periodId.value]) || [];
});

// Валидация формы
const canSave = computed(() =>
    name.value &&
    type.value &&
    periodId.value &&
    parseFloat(cost.value) >= 0,
);

// Сброс формы
const resetForm = () => {
    id.value       = null;
    name.value     = null;
    type.value     = null;
    periodId.value = null;
    cost.value     = null;
    active.value   = false;
    actions.value  = null;
};

// Закрытие диалога
const closeDialog = () => {
    showDialog.value = false;
    resetForm();
};

// Обработчик изменения периода
const onPeriodChange = () => {
    clearError('period_id');
    // Сбрасываем выбранный тип при смене периода, если это необходимо
    type.value = null;
};

// Следим за изменением modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            id.value       = newValue.id;
            name.value     = newValue.name;
            type.value     = newValue.type;
            periodId.value = newValue.periodId;
            cost.value     = newValue.cost;
            active.value   = newValue.active;
            actions.value  = newValue.actions;
        }
        else {
            resetForm();
        }
    },
    { immediate: true },
);

// Сохранение
const saveAction = async () => {
    loading.value = true;
    const data    = {
        id       : props.modelValue?.id,
        name     : name.value,
        type     : type.value,
        period_id: periodId.value,
        cost     : parseFloat(cost.value).toFixed(2),
        active   : active.value,
    };

    try {
        const response = await ApiAdminServiceSave({}, data);
        emit('update:modelValue', response.data.service);
        showInfo(props.modelValue?.id ? 'Услуга обновлена' : 'Услуга создана');
        closeDialog();
    }
    catch (error) {
        const message = error?.response?.data?.message ||
            `Не удалось ${props.modelValue?.id ? 'обновить' : 'создать'} услугу`;
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};
</script>