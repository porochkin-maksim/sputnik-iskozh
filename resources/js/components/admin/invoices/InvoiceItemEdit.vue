<template>
    <view-dialog
        v-model:show="showDialog"
        v-model:hide="hideDialog"
        @hidden="closeDialog"
    >
        <template #title>
            {{ id ? 'Редактирование счёта' : 'Добавление счёта' }}
        </template>
        <template #body>
            <div class="container-fluid">
                <!-- Название -->
                <div class="mb-2">
                    <custom-input
                        v-model="name"
                        :errors="errors?.name"
                        label="Название (опционально)"
                        type="text"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Тип -->
                <div class="mb-2">
                    <custom-select
                        v-model="type"
                        :errors="errors?.type"
                        label="Тип"
                        :options="props.types"
                        :required="true"
                        @update:modelValue="clearError('type')"
                    />
                </div>

                <!-- Период -->
                <div class="mb-2">
                    <custom-select
                        v-model="periodId"
                        :errors="errors?.period_id"
                        label="Период"
                        :options="props.periods"
                        :required="true"
                        @update:modelValue="clearError('period_id')"
                    />
                </div>

                <!-- Участок -->
                <div class="mb-2">
                    <search-select
                        :items="props.accounts"
                        :prop-class="'form-control'"
                        label="Участок"
                        v-model="accountId"
                        :error="errors?.account_id"
                        @update:modelValue="clearError('account_id')"
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
                {{ id ? 'Сохранить' : 'Создать' }} счёт
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    defineProps,
    defineEmits,
    defineOptions,
    watch,
}                              from 'vue';
import { useResponseError }    from '@composables/useResponseError';
import CustomInput             from '../../common/form/CustomInput.vue';
import SearchSelect            from '../../common/form/SearchSelect.vue';
import ViewDialog              from '../../common/ViewDialog.vue';
import { ApiAdminInvoiceSave } from '@api';
import CustomSelect            from '@common/form/CustomSelect.vue';

defineOptions({
    name: 'InvoiceItemEdit',
});

const props = defineProps({
    modelValue: {
        type    : Object,
        required: true,
    },
    accounts  : {
        type    : Array,
        required: true,
    },
    periods   : {
        type    : Array,
        required: true,
    },
    types     : {
        type    : Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'updated']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

const showDialog = ref(true);
const hideDialog = ref(false);
const loading    = ref(false);

const id        = ref(null);
const periodId  = ref(null);
const accountId = ref(null);
const type      = ref(null);
const name      = ref('');

// Инициализация из modelValue
const initFromModel = () => {
    id.value        = props.modelValue.id || null;
    periodId.value  = props.modelValue.periodId || null;
    accountId.value = props.modelValue.accountId || null;
    type.value      = props.modelValue.type || null;
    name.value      = props.modelValue.name || '';

    // Если тип не выбран и есть доступные типы
    if (!type.value && props.types?.length) {
        const firstOption = props.types[0];
        type.value        = firstOption.value ?? firstOption;
    }
};

// Следим за изменениями modelValue
watch(() => props.modelValue, initFromModel, { immediate: true, deep: true });

// Валидация формы
const canSave = computed(() => {
    return type.value && periodId.value && accountId.value;
});

// Закрытие диалога
const closeDialog = () => {
    showDialog.value = false;
};

// Сохранение
const saveAction = async () => {
    loading.value = true;

    const data = {
        id        : id.value,
        period_id : periodId.value,
        account_id: accountId.value,
        type      : type.value,
        name      : name.value,
    };

    try {
        const response = await ApiAdminInvoiceSave({}, data);
        const message  = id.value ? 'Счёт обновлён' : `Счёт ${response.data.invoice.id} создан`;
        showInfo(message);
        emit('updated');
        closeDialog();
    }
    catch (error) {
        const message = error?.response?.data?.message ||
            `Не удалось ${id.value ? 'сохранить' : 'создать'} счёт`;
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

onMounted(initFromModel);
</script>