<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>{{ localHistory?.id ? 'Изменение показаний счётчика' : 'Внесение показаний счётчика' }}
        </template>
        <template v-slot:body>
            <div class="container-fluid">
                <div v-if="counter.isInvoicing"
                     class="alert alert-info">
                    <template v-if="localHistory?.id">
                        При обновлении показаний будет автоматически пересчитана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                    <template v-else>
                        При добавлении показаний будет автоматически создана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                </div>
                <div class="mt-2">
                    <custom-input v-model="formData.value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <label class="text-secondary">Дата показаний</label>
                    <custom-calendar v-model="formData.date"
                                     :error="errors.date"
                                     :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger"
                                @click="removeFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary"
                                @click="chooseFile"
                                v-if="!file">
                            <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="fileElem"
                               accept="image/*"
                               @change="appendFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSubmitAction"
                    @click="saveAction">
                {{ localHistory?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    onMounted,
}                           from 'vue';
import CustomInput          from '../../../common/form/CustomInput.vue';
import ViewDialog           from '../../../common/ViewDialog.vue';
import CustomCalendar       from '../../../common/form/CustomCalendar.vue';
import { useResponseError } from '@composables/useResponseError'; // если используется такой композабл, иначе импортируйте миксин
import {
    ApiAdminCounterAddValue,
}                           from '@api';

const { parseResponseErrors, showSuccess, showDanger } = useResponseError();

const props = defineProps({
    counter: {
        type   : Object,
        default: null,
    },
    history: {
        type   : Object,
        default: null,
    },
});

const emit = defineEmits(['historyUpdated']);

// Состояния
const loading    = ref(false);
const showDialog = ref(true);
const hideDialog = ref(false);
const file       = ref(null);
const fileElem   = ref(null);

// Форма
const formData = reactive({
    id   : null,
    value: null,
    date : '',
});

// Ошибки (можно использовать простой объект)
const errors = reactive({
    value: null,
    date : null,
});

// Локальная копия истории для отображения в заголовке
const localHistory = computed(() => props.history);

// Инициализация
const initForm = () => {
    if (props.history) {
        formData.id    = props.history.id;
        formData.value = props.history.value;
        formData.date  = props.history.date;
    }
    else {
        const date     = new Date();
        formData.id    = null;
        formData.value = props.counter?.value ?? null;
        formData.date  = props.counter?.date ?? date.toISOString().split('T')[0];
    }
};

// Валидация
const canSubmitAction = computed(() => {
    return !loading.value && formData.value && formData.date;
});

// Методы
const saveAction = async () => {
    loading.value = true;
    errors.value  = null;
    errors.date   = null;

    const form = new FormData();
    form.append('counter_id', props.counter.id);
    form.append('id', formData.id);
    form.append('value', formData.value);
    form.append('date', formData.date);
    if (file.value) {
        form.append('file', file.value);
    }

    try {
        await ApiAdminCounterAddValue(props.counter.accountId, {}, form);
        onSuccessSubmit();
        const message = formData.id ? 'Показания обновлены' : 'Показания добавлены';
        // Используйте вашу систему уведомлений, например, showInfo
        showSuccess(message);
    }
    catch (error) {
        // Предполагаем, что есть метод parseResponseErrors (можно импортировать из composable)
        if (typeof parseResponseErrors === 'function') {
            parseResponseErrors(error);
        }
        else {
            showDanger(error);
        }
    }
    finally {
        loading.value = false;
    }
};

const onSuccessSubmit = () => {
    showDialog.value = false;
    hideDialog.value = true;
    file.value       = null;
    emit('historyUpdated');
};

const closeAction = () => {
    showDialog.value = false;
    emit('historyUpdated');
};

const chooseFile = () => {
    fileElem.value?.click();
};

const appendFile = (event) => {
    file.value = event.target.files[0];
};

const removeFile = () => {
    file.value = null;
};

onMounted(() => {
    initForm();
});
</script>