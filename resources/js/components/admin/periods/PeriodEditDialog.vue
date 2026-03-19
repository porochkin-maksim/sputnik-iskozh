<template>
    <view-dialog
        v-model:show="showDialog"
        v-model:hide="hideDialog"
        @hidden="closeDialog"
    >
        <template #title>
            {{ props.modelValue?.id ? 'Редактирование периода' : 'Создание периода' }}
        </template>
        <template #body>
            <div class="container-fluid">
                <!-- Название периода -->
                <div>
                    <custom-input
                        v-model="name"
                        :errors="errors?.name"
                        type="text"
                        label="Название"
                        :required="true"
                        :disabled="props.modelValue?.isClosed"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Начало периода с календарём и временем -->
                <div class="mt-2">
                    <custom-calendar
                        v-model="startAt"
                        :error="errors?.start_at"
                        label="Начало периода"
                        :required="true"
                        :disabled="props.modelValue?.isClosed"
                        with-time
                        @update:modelValue="clearError('start_at')"
                    />
                </div>

                <!-- Окончание периода с календарём и временем -->
                <div class="mt-2">
                    <custom-calendar
                        v-model="endAt"
                        :error="errors?.end_at"
                        label="Окончание периода"
                        :required="true"
                        :disabled="props.modelValue?.isClosed"
                        with-time
                        @update:modelValue="clearError('end_at')"
                        withTime
                    />
                </div>

                <!-- Чекбокс закрытия периода -->
                <div class="mt-2">
                    <custom-checkbox
                        v-model="isClosed"
                        :errors="errors?.is_closed"
                        label="Закрыть период"
                        :disabled="!props.modelValue?.id"
                        @update:modelValue="clearError('is_closed')"
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
    defineOptions,
}                             from 'vue';
import { useResponseError }   from '@composables/useResponseError';
import ViewDialog             from '../../common/ViewDialog.vue';
import CustomInput            from '@common/form/CustomInput.vue';
import CustomCheckbox         from '@common/form/CustomCheckbox.vue';
import CustomCalendar         from '@common/form/CustomCalendar.vue';
import { ApiAdminPeriodSave } from '@api';

const props = defineProps({
    modelValue: {
        type   : Object,
        default: null,
    },
    show      : {
        type   : Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'update:show']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

const name       = ref(null);
const startAt    = ref(null);
const endAt      = ref(null);
const isClosed   = ref(false);
const loading    = ref(false);
const hideDialog = ref(false);

// Двусторонняя привязка show
const showDialog = computed({
    get: () => props.show,
    set: (value) => emit('update:show', value),
});

// Валидация формы
const canSave = computed(() => name.value && startAt.value && endAt.value);

// Сброс формы
const resetForm = () => {
    name.value     = null;
    startAt.value  = null;
    endAt.value    = null;
    isClosed.value = false;
};

// Закрытие диалога
const closeDialog = () => {
    showDialog.value = false;
    resetForm();
};

// Следим за изменением modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue) {
            name.value     = newValue.name;
            startAt.value  = newValue.startAt;
            endAt.value    = newValue.endAt;
            isClosed.value = newValue.isClosed;
        }
        else {
            resetForm();
        }
    },
    { immediate: true },
);

// Сохранение
const saveAction = async () => {
    if (isClosed.value) {
        if (!confirm('Вы уверены что хотите закрыть период? Это необратимое действие!')) {
            return;
        }
    }

    loading.value = true;
    const data    = {
        id       : props.modelValue?.id,
        name     : name.value,
        start_at : startAt.value,
        end_at   : endAt.value,
        is_closed: isClosed.value,
    };

    try {
        const response = await ApiAdminPeriodSave({}, data);
        emit('update:modelValue', response.data.period);
        showInfo(props.modelValue?.id ? 'Период обновлен' : 'Период создан');
        closeDialog();
    }
    catch (error) {
        const message = error?.response?.data?.message ||
            `Не удалось ${props.modelValue?.id ? 'обновить' : 'создать'} период`;
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};
</script>