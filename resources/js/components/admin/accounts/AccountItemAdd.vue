<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="onCloseDialog">
        <template v-slot:title>Добавление участка</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="formData.number"
                                  :errors="errors.number"
                                  :required="true"
                                  :label="'Номер участка'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="formData.size"
                                  :errors="errors.size"
                                  :label="'Площадь (м²)'"
                                  :type="'number'"
                                  :min="0"
                                  :step="1"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-checkbox v-model="formData.isInvoicing"
                                     :label="'Выставлять счета'"
                                     switch-style
                    />
                </div>
                <div>
                    <custom-input v-model="formData.cadastreNumber"
                                  :errors="errors.cadastreNumber"
                                  :label="'Кадастровый номер'"
                    />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave || loading"
                    @click="saveAction">
                <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ loading ? 'Создание...' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    watch,
    onMounted,
}                              from 'vue';
import CustomInput             from '../../common/form/CustomInput.vue';
import CustomCheckbox          from '../../common/form/CustomCheckbox.vue';
import ViewDialog              from '../../common/ViewDialog.vue';
import { useResponseError }    from '@composables/useResponseError';
import { ApiAdminAccountSave } from '@api';

const props = defineProps({
    modelValue: {
        type   : Object,
        default: null,
    },
});

const emit = defineEmits(['updated']);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

// Состояния
const loading    = ref(false);
const showDialog = ref(false);
const hideDialog = ref(false);

// Форма
const formData = reactive({
    number        : null,
    size          : null,
    isInvoicing   : false,
    cadastreNumber: null,
});

// Ошибки
const errors = reactive({
    number        : null,
    size          : null,
    cadastreNumber: null,
});

// Инициализация
const initForm = () => {
    if (props.modelValue) {
        formData.number         = props.modelValue.number;
        formData.size           = props.modelValue.size;
        formData.isInvoicing    = props.modelValue.is_invoicing;
        formData.cadastreNumber = props.modelValue.cadastreNumber;
        showDialog.value        = true;
        hideDialog.value        = false;
    }
    else {
        resetForm();
        showDialog.value = true;
        hideDialog.value = false;
    }
};

// Сброс формы
const resetForm = () => {
    formData.number         = null;
    formData.size           = null;
    formData.isInvoicing    = false;
    formData.cadastreNumber = null;
    errors.number           = null;
    errors.size             = null;
    errors.cadastreNumber   = null;
};

// Валидация
const canSave = computed(() => {
    return formData.number && formData.size !== null && formData.size >= 0;
});

// Сохранение
const saveAction = async () => {
    loading.value         = true;
    errors.number         = null;
    errors.size           = null;
    errors.cadastreNumber = null;

    const form = new FormData();
    form.append('number', formData.number);
    form.append('size', parseInt(formData.size ? formData.size : 0));
    form.append('is_invoicing', !!formData.isInvoicing);
    form.append('cadastreNumber', formData.cadastreNumber || '');

    try {
        const response = await ApiAdminAccountSave({}, form);
        showInfo('Участок ' + response.data.account.id + ' создан');
        emit('updated');
        onCloseDialog();
    }
    catch (error) {
        const text = error?.response?.data?.message || 'Не получилось создать участок';
        showDanger(text);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Закрытие диалога
const onCloseDialog = () => {
    showDialog.value = false;
    hideDialog.value = true;
    loading.value    = false;
    resetForm();
};

// Следим за modelValue
watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        initForm();
    }
}, { immediate: true });

onMounted(() => {
    if (props.modelValue) {
        initForm();
    }
});
</script>

<style scoped>
.index {
    width : 80px;
}

.size {
    width : 50px;
}
</style>